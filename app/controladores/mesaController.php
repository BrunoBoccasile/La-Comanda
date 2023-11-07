<?php

require_once __DIR__ . '/../models/mesa.php';

class MesaController {

    public function insertarMesa($estado) {
        $mesa = new Mesa();
        $mesa->estado = $estado;
        return $mesa->InsertarLaMesaParametros();
    }

    public function modificarMesa($id, $estado) {
        $mesa = new Mesa();
        $mesa->id = $id;
        $mesa->estado = $estado;
        return $mesa->ModificarMesaParametros();
    }

    public function borrarMesa($id) {
        $mesa = new Mesa();
        $mesa->id = $id;
        return $mesa->BorrarMesa();
    }

    
    public function listarMesas() {
        return Mesa::TraerTodasLasMesas();
    }
    
    
    public function listarMesasJson() {
        return json_encode(Mesa::TraerTodasLasMesas(), JSON_PRETTY_PRINT);
    }
    

    public function buscarMesaPorId($id) {
        $retorno = Mesa::TraerUnaMesa($id);
        return $retorno;
    }

}