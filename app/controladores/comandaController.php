<?php

require_once __DIR__ . '/../models/comanda.php';
require_once __DIR__ . '/../controladores/mesaController.php';
require_once __DIR__ . '/../controladores/productoController.php';
require_once __DIR__ . '/../controladores/clienteController.php';

class ComandaController {
    public function insertarComanda($detalle, $idMesa, $idCliente) {
        $comanda = new Comanda();
        $controladorMesa = new MesaController();
        $controladorCliente = new ClienteController();
        $controladorProducto = new ProductoController();
        $mesaRetornada = $controladorMesa->buscarMesaPorId($idMesa);
        $clienteRetornado = $controladorCliente->buscarClientePorId($idCliente);
        if($clienteRetornado != false)
        {
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
                    $comanda->idCliente = $idCliente;
                    $comanda->nombreCliente = $clienteRetornado->nombre;
                    $comanda->detalle = $detalle;
                    for($i=0 ; $i<count(explode(",", $detalle)) ; $i++)
                    {
                        if($i == 0)
                        {
                            $comanda->estado = "pendiente";
                            $comanda->tiempoEstimadoFinalizacion = 0;
                        }
                        else
                        {
                            $comanda->estado .= "," . "pendiente";
                            $comanda->tiempoEstimadoFinalizacion .= "," . 0;
                        }
                    }
                    $comanda->idMesa = $idMesa;
                    $comanda->costoTotal =  Comanda::calcularCostoTotal($detalle);
                    $comanda->fechaHoraCreacion = date('y-m-d H:i');
    
                    $controladorMesa->modificarMesa($idMesa, "con cliente esperando pedido");
    
            }
            else
            {
                return 0;
            }
        }
        else
        {
            return -1;
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
            $comandaOriginal->costoTotal = Comanda::calcularCostoTotal($detalle);
        }
        else
        {
            return false;
        }
        return $comandaOriginal->ModificarComandaParametros();
    }

    public function bajaComanda($id) {
        $comandaRetornada = self::buscarComandaPorId($id);
        $arrayDetalle = explode(",", $comandaRetornada->detalle);
        for($i=0 ; $i<count($arrayDetalle) ; $i++)
        {
            if($i == 0)
            {
                $comandaRetornada->estado = "concluida";
            }
            else
            {
                $comandaRetornada->estado .= ",". "concluida";
            }
        }
        return $comandaRetornada->ModificarComandaParametros();
    }

    
    public function listarProductosComanda() 
    {
        return Comanda::TraerTodosLosProductosComanda();
    }

    public function listarProductosComandaPorEstado($estado) 
    {
        return Comanda::TraerTodosLosProductosComandaPorEstado($estado);
    }
    
    public function listarComandasPorMesa($idMesa) 
    {
        return Comanda::TraerTodasLasComandasPorMesa($idMesa);
    }

    public function listarProductosComandaHistoricos()
    {
        return Comanda::TraerTodosLosProductosComandaHistoricos();
    }

    public function listarComandasHistoricas()
    {
        $productosComanda = self::listarProductosComandaHistoricos();
        $arrayId = array();
        $arrayComandas = array();
        if($productosComanda)
        {
            foreach($productosComanda as $productoComanda)
            {
                array_push($arrayId, $productoComanda->id);
            }
            $arrayIdUnico = array_unique($arrayId);
            foreach($arrayIdUnico as $id)
            {
                $comandaRetornada = self::buscarComandaPorId($id);
                if($comandaRetornada)
                {
                    array_push($arrayComandas, $comandaRetornada);
                }
                else
                {
                    $comandaRetornada = self::buscarComandaConcluidaPorId($id);
                    if($comandaRetornada)
                    {
                        array_push($arrayComandas, $comandaRetornada);
                    }
                }
            }
        }
        else
        {
            return false;
        }
        return $arrayComandas;
    }
    
    public function listarProductosComandaPorTipo($tipo)
    {
        return Comanda::TraerTodosLosProductosComandaPorTipo($tipo);
    }
    public function listarProductosComandaPorTipoYEstado($tipo, $estado)
    {
        return Comanda::TraerTodosLosProductosComandaPorTipoYEstado($tipo, $estado);
    }
    

    public function buscarComandaPorId($id) 
    {
        $comandaRetornada = Comanda::TraerUnaComandaPorId($id);
        $nuevaComanda = false;
        if($comandaRetornada)
        {

            $flagPrimero = false;
            foreach($comandaRetornada as $comanda)
            {
                if(!$flagPrimero)
                {
                    $flagPrimero = true;
                    $stringDetalles = $comanda->detalle;
                    $stringTiempos =$comanda->tiempoEstimadoFinalizacion;
                    $stringIdProducto = $comanda->idProducto;
                    $stringEstado = $comanda->estado;
                }
                else
                {
                    $stringDetalles .= "," . $comanda->detalle;
                    $stringTiempos .= "," . $comanda->tiempoEstimadoFinalizacion;
                    $stringIdProducto .= "," . $comanda->idProducto;
                    $stringEstado .= "," . $comanda->estado;
                }
            }
            $nuevaComanda = new Comanda();
            $nuevaComanda->id = $comandaRetornada[0]->id;
            $nuevaComanda->idCliente = $comandaRetornada[0]->idCliente;
            $nuevaComanda->nombreCliente = $comandaRetornada[0]->nombreCliente;
            $nuevaComanda->idProducto = $stringIdProducto;
            $nuevaComanda->detalle = $stringDetalles;
            $nuevaComanda->estado = $stringEstado;
            $nuevaComanda->tiempoEstimadoFinalizacion = $stringTiempos;
            $nuevaComanda->idMesa = $comandaRetornada[0]->idMesa;
            $nuevaComanda->costoTotal = $comandaRetornada[0]->costoTotal;
            $nuevaComanda->fechaHoraCreacion = $comandaRetornada[0]->fechaHoraCreacion;
        }
        return $nuevaComanda;
    }

    public function buscarComandaConcluidaPorId($id) 
    {
        $comandaRetornada = Comanda::TraerUnaComandaConcluidaPorId($id);
        $nuevaComanda = false;
        if($comandaRetornada)
        {

            $flagPrimero = false;
            foreach($comandaRetornada as $comanda)
            {
                if(!$flagPrimero)
                {
                    $flagPrimero = true;
                    $stringDetalles = $comanda->detalle;
                    $stringTiempos =$comanda->tiempoEstimadoFinalizacion;
                    $stringIdProducto = $comanda->idProducto;
                    $stringEstado = $comanda->estado;
                }
                else
                {
                    $stringDetalles .= "," . $comanda->detalle;
                    $stringTiempos .= "," . $comanda->tiempoEstimadoFinalizacion;
                    $stringIdProducto .= "," . $comanda->idProducto;
                    $stringEstado .= "," . $comanda->estado;
                }
            }
            $nuevaComanda = new Comanda();
            $nuevaComanda->id = $comandaRetornada[0]->id;
            $nuevaComanda->idCliente = $comandaRetornada[0]->idCliente;
            $nuevaComanda->nombreCliente = $comandaRetornada[0]->nombreCliente;
            $nuevaComanda->idProducto = $stringIdProducto;
            $nuevaComanda->detalle = $stringDetalles;
            $nuevaComanda->estado = $stringEstado;
            $nuevaComanda->tiempoEstimadoFinalizacion = $stringTiempos;
            $nuevaComanda->idMesa = $comandaRetornada[0]->idMesa;
            $nuevaComanda->costoTotal = $comandaRetornada[0]->costoTotal;
            $nuevaComanda->fechaHoraCreacion = $comandaRetornada[0]->fechaHoraCreacion;
        }
        return $nuevaComanda;
    }

    public function PrepararProductoComanda($comandaProducto)
    {
        $comandaProducto->tiempoEstimadoFinalizacion = Comanda::calcularTiempoEstimadoFinalizacion($comandaProducto);
        return $comandaProducto->PreparacionProductoComanda();
    }

    public function buscarProductoComanda($idProducto)
    {
        return Comanda::TraerProductoComanda($idProducto);
    }
}