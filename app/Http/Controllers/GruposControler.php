<?php

namespace App\Http\Controllers;

use App\Models\GruposModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GruposControler extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //  
 

    }

    
    public function exibirGrupos() {
        $grupo = GruposModel::all(); 
        return view('grupos', compact('grupo'));
    }


    public function entrarNoGrupo($grupoId)
    {
        $usuarioId = Auth::id(); 
        $grupo = GruposModel::findOrFail($grupoId);
        $grupo->usuarios()->syncWithoutDetaching([$usuarioId]);

        return redirect()->back()->with('success', 'Você entrou no grupo com sucesso!');
    }
    
    public function criarGrupo(Request $request)
    {
        //  
        $grupo = new GruposModel();
        $grupo->idLider = Auth::id();
        $grupo ->nomeGrupo = $request->input('nomeGrupo');
        $grupo ->descGrupo = $request->input('descGrupo');
        $image=$request->file('foto');
        if($image==null){
            $path="";
        }else{
        $path = $image->store('arquivosGrupo', 'public');
        }
        $grupo->imagemGrupo = $path;
        $grupo->save();
        $grupo->usuarios()->syncWithoutDetaching([Auth::id()]);

        return redirect('grupo')->with('success', 'Mangá inserido com sucesso!');

    }




    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
