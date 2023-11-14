<?php

require_once __DIR__ . '/../models/comanda.php';
require_once __DIR__ . '/../controladores/mesaController.php';
require_once __DIR__ . '/../controladores/productoController.php';

class ComandaController {


    public $id;//alfanumerico 5 caracteres
    public $detalle;//array asociativo ej:(trago => 1, cerveza => 3, comida => 4, postre => 2)
    public $estado;//pendiente/en preparacion/listo para servir
    public $tiempoEstimadoFinalizacion;//en minutos, varia segun cantidad de empleados
    public $idMesa;
    public $costoTotal;
    public $fechaHoraCreacion;

    public function insertarComanda($detalle, $idMesa) {
        $comanda = new Comanda();
        $controladorMesa = new MesaController();
        $mesaRetornada = $controladorMesa->buscarMesaPorId($idMesa);
        if($mesaRetornada != false)
        {
    
            $comandasDeLaMesa = $comanda->TraerTodasLasComandasPorMesa($idMesa);

            do
            {
                $id = Comanda::generarIdAlfanumerico();
            }
            while(self::buscarComandaPorId($id) != false);
            
            $controladorProducto = new ProductoController();
            $comanda->id = $id;
            $comanda->detalle = $detalle;
            $comanda->estado = "pendiente";
            $comanda->tiempoEstimadoFinalizacion = Comanda::calcularTiempoEstimadoFinalizacion($detalle);
            $comanda->idMesa = $idMesa;
            $comanda->costoTotal = ($controladorProducto->buscarProductoPorNombre($detalle))->precio;
            $comanda->fechaHoraCreacion = date('y-m-d H:i');

            $controladorMesa->modificarMesa($idMesa, "con cliente esperando pedido");
        }
        else
        {
            return false;
        }

        return $comanda->InsertarLaComandaParametros();
    }

    public function modificarComanda($id, $detalle, $idMesa, $estado) {
        $comanda = new Comanda();
        $controladorMesa = new MesaController();
        $comandaOriginal = self::buscarComandaPorId($id);

        if($controladorMesa->buscarMesaPorId($idMesa) != false)
        {
            $controladorProducto = new ProductoController();
            $comandaOriginal->detalle = $detalle;
            $comandaOriginal->estado = $estado;
            $comandaOriginal->idMesa = $idMesa;
            $comandaOriginal->costoTotal = ($controladorProducto->buscarProductoPorNombre($detalle))->precio;
            $comandaOriginal->tiempoEstimadoFinalizacion = Comanda::calcularTiempoEstimadoFinalizacion($detalle);
        }
        else
        {
            return false;
        }
        return $comandaOriginal->ModificarComandaParametros();
    }

    public function borrarComanda($id) 
    {
        $comanda = new Comanda();
        $comanda->id = $id;
        return $comanda->BorrarComanda();
    }

    
    public function listarComandas() 
    {
        return Comanda::TraerTodasLasComandas();
    }

    public function listarComandasPorMesa($idMesa) 
    {
        return Comanda::TraerTodasLasComandasPorMesa($idMesa);
    }
    
    public function listarComandasPorTipo($tipo)
    {
        return Comanda::TraerTodasLasComandasPorTipo($tipo);
    }

    public function listarComandasJson() 
    {
        return json_encode(Comanda::TraerTodasLasComandas(), JSON_PRETTY_PRINT);
    }
    

    public function buscarComandaPorId($id) 
    {
        $retorno = Comanda::TraerUnaComanda($id);
        return $retorno;
    }

}