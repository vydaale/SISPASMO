<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Aspirante;
use Illuminate\Support\Str;
use App\Notifications\CredencialesAspirante;
use App\Models\Alumno;
use App\Models\Diplomado;
use Illuminate\Contracts\Queue\ShouldQueue;

class AspiranteController extends Controller
{
    public function index()
    {
        $aspirantes = Aspirante::with('usuario')->orderByDesc('id_aspirante')->paginate(15);
        return view('administrador.CRUDAspirantes.read', compact('aspirantes'));
    }
    public function create()
    {
        return view('administrador.CRUDAspirantes.create');
    }

    public function edit(Aspirante $aspirante)
    {
        $aspirante->load('usuario');
        return view('administrador.CRUDAspirantes.update', compact('aspirante'));
    }

    public function update(Request $request, Aspirante $aspirante)
    {
        $estatusAnterior = $aspirante->estatus;

        // Si va a aceptado, ya NO exigimos 'usuario' del form; lo pondremos = matrícula
        $rules = [
            'nombre'       => ['required', 'string', 'max:100'],
            'apellidoP'    => ['required', 'string', 'max:100'],
            'apellidoM'    => ['required', 'string', 'max:100'],
            'fecha_nac'    => ['required', 'date'],
            // clave: required_unless (si NO va a aceptado, sí pedimos usuario del form)
            'usuario'      => [
                'required_unless:estatus,aceptado',
                'string',
                'max:50',
                Rule::unique('usuarios', 'usuario')->ignore($aspirante->usuario->id_usuario, 'id_usuario')
            ],
            'genero'       => ['required', Rule::in(['M', 'F', 'Otro'])],
            'correo'       => [
                'required',
                'email',
                'max:100',
                Rule::unique('usuarios', 'correo')->ignore($aspirante->usuario->id_usuario, 'id_usuario')
            ],
            'telefono'     => ['required', 'string', 'max:20'],
            'direccion'    => ['required', 'string', 'max:100'],
            'interes'      => ['required', 'string', 'max:50'], 
            'dia'          => ['required', 'date'],
            'estatus'      => ['required', Rule::in(['activo', 'rechazado', 'aceptado'])],
            'id_diplomado' => ['nullable', 'integer', 'exists:diplomados,id_diplomado'], 
        ];

        $messages = [
            'usuario.required_unless' => 'El campo usuario es obligatorio salvo cuando aceptas al aspirante.',
        ];

        $data = $request->validate($rules, $messages);

        $vaASerAceptado = ($estatusAnterior !== 'aceptado' && $data['estatus'] === 'aceptado');

        // 1) Resolver id_diplomado ANTES de la transacción si va a aceptado
        $idDiplomado = $data['id_diplomado'] ?? null;
        if ($vaASerAceptado && !$idDiplomado) {
            $idDiplomado = Diplomado::where('nombre', $data['interes'])->value('id_diplomado')
                ?? Diplomado::where('nombre', 'LIKE', $data['interes'] . '%')->value('id_diplomado');
            if (!$idDiplomado) {
                return back()
                    ->withErrors(['interes' => 'No se encontró un diplomado con ese nombre. Selecciona uno válido o envía id_diplomado.'])
                    ->withInput();
            }
        }

        DB::transaction(function () use ($data, $aspirante, $vaASerAceptado, $idDiplomado) {

            // 2) Actualiza datos base de Usuario/Aspirante
            $aspirante->usuario->update([
                'nombre'    => $data['nombre'],
                'apellidoP' => $data['apellidoP'],
                'apellidoM' => $data['apellidoM'],
                'fecha_nac' => $data['fecha_nac'],
                'usuario'   => $vaASerAceptado
                    ? $aspirante->usuario->usuario 
                    : $data['usuario'],
                'genero'    => $data['genero'],
                'correo'    => $data['correo'],
                'telefono'  => $data['telefono'],
                'direccion' => $data['direccion'],
            ]);

            $aspirante->update([
                'interes' => $data['interes'],
                'dia'     => $data['dia'],
                'estatus' => $data['estatus'],
            ]);

            // 3) Si pasó a "aceptado": generar matrícula, credenciales, alumno y rol
            if ($vaASerAceptado) {

                // 3.1 Generar matrícula única
                $matricula = $this->generarMatriculaUnica((int)$idDiplomado);

                // 3.2 Poner usuario = matrícula
                $aspirante->usuario->usuario = $matricula;

                // 3.3 Generar y guardar contraseña temporal
                $plain = Str::password(10);
                $aspirante->usuario->pass = Hash::make($plain);
                $aspirante->usuario->save(); 

                // 3.4 Crear alumno si no existe
                $yaEsAlumno = Alumno::where('id_usuario', $aspirante->id_usuario)->exists();
                if (!$yaEsAlumno) {
                    Alumno::create([
                        'id_usuario'   => $aspirante->id_usuario,
                        'matriculaA'   => $matricula,              
                        'id_diplomado' => (int)$idDiplomado,
                        'estatus'      => 'activo',
                    ]);
                }

                // 3.5 Cambiar rol a alumno (ajusta al ID real)
                $ROL_ALUMNO = 4; // Asegúrate de que este es el ID del rol de Alumno
                if ((int)$aspirante->usuario->id_rol !== $ROL_ALUMNO) {
                    $aspirante->usuario->id_rol = $ROL_ALUMNO;
                    $aspirante->usuario->save();
                }

                // 3.6 ENVÍO DE NOTIFICACIÓN (Se guarda en DB y se va a la cola de correos)
                $nombre   = trim(($aspirante->usuario->nombre ?? '') . ' ' . ($aspirante->usuario->apellidoP ?? '') . ' ' . ($aspirante->usuario->apellidoM ?? ''));
                $loginUrl = route('inicio'); // <-- tu ruta de login
                $aspirante->usuario->notify(
                    new CredencialesAspirante($nombre, $matricula, $plain, $loginUrl)
                );
            }
        });

        return redirect()->route('aspirantes.index')->with('success', 'Aspirante actualizado exitosamente.');
    }



    private function generarMatriculaUnica(int $idDiplomado): string
    {
        $anio = now()->format('Y');
        $prefijoDip = 'D' . $idDiplomado;

        $consecutivo = Alumno::where('id_diplomado', $idDiplomado)->count() + 1;
        $num = str_pad((string)$consecutivo, 4, '0', STR_PAD_LEFT);
        $mat = "A-{$anio}-{$prefijoDip}-{$num}";

        while (Alumno::where('matriculaA', $mat)->exists()) {
            $consecutivo++;
            $num = str_pad((string)$consecutivo, 4, '0', STR_PAD_LEFT);
            $mat = "A-{$anio}-{$prefijoDip}-{$num}";
        }
        return $mat;
    }



    public function store(Request $request)
    {
        $data = $request->validate([
            // USUARIO
            'nombre'       => ['required', 'string', 'max:100'],
            'apellidoP'    => ['required', 'string', 'max:100'],
            'apellidoM'    => ['required', 'string', 'max:100'],
            'fecha_nac'    => ['required', 'date'],
            'usuario'      => ['required', 'string', 'max:50', 'unique:usuarios,usuario'],
            'pass'         => ['required', 'string', 'min:8', 'confirmed'],
            'genero'       => ['required', Rule::in(['M', 'F', 'Otro'])],
            'correo'       => ['required', 'email', 'max:100', 'unique:usuarios,correo'],
            'telefono'     => ['required', 'string', 'max:20'],
            'direccion'    => ['required', 'string', 'max:100'],
            'id_rol'       => ['required', 'integer'],
            'interes' => ['required', 'string', 'max:50'],
            'dia'     => ['required', 'date'],
            'estatus' => ['required', Rule::in(['activo', 'rechazado'])],
        ]);
        DB::transaction(function () use ($data) {
            $usuario = User::create([
                'nombre'      => $data['nombre'],
                'apellidoP'   => $data['apellidoP'],
                'apellidoM'   => $data['apellidoM'],
                'fecha_nac'   => $data['fecha_nac'],
                'usuario'     => $data['usuario'],
                'pass'        => Hash::make($data['pass']),
                'genero'      => $data['genero'],
                'correo'      => $data['correo'],
                'telefono'    => $data['telefono'],
                'direccion'   => $data['direccion'],
                'id_rol'      => $data['id_rol'],
            ]);
            Aspirante::create([
                'id_usuario' => $usuario->id_usuario,
                'interes'    => $data['interes'],
                'dia'        => $data['dia'],
                'estatus'    => $data['estatus'],
            ]);
        });
        return redirect()->route('aspirantes.index')->with('success', 'Aspirante creado exitosamente.');
    }

    public function destroy(Aspirante $aspirante)
    {
        DB::transaction(function () use ($aspirante) {
            $usuario = $aspirante->usuario;
            $aspirante->delete();

            if ($usuario) {
                $usuario->delete();
            }
        });

        return redirect()->route('aspirantes.index')->with('ok', 'Aspirante eliminado.');
    }



    public function dashboard()
    {
        $userId = auth()->user()->id_usuario;
        $aspirante = Aspirante::where('id_usuario', $userId)->with('usuario')->firstOrFail();

        return view('aspirante.dashboardaspirante', compact('aspirante'));
    }
}