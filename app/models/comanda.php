<?php
include_once __DIR__ . "/../controladores/productoController.php";
include_once __DIR__ . "/../controladores/empleadoController.php";

class Comanda
{
    public $id;//alfanumerico 5 caracteres
    public $idCliente;
    public $nombreCliente;
    public $idProducto;
    public $detalle;//array asociativo ej:(trago => 1, cerveza => 3, comida => 4, postre => 2)
    public $estado;//pendiente/en preparacion/listo para servir
    public $tiempoEstimadoFinalizacion;//varia segun cantidad de empleados
    public $idMesa;
    public $costoTotal;
    public $fechaHoraCreacion;

    public function mostrarProductoComanda()
    {
        return json_encode(array("ID" => $this->id, "ID CLIENTE" => $this->idCliente, "NOMBRE CLIENTE" => $this->nombreCliente, "ID PRODUCTO" => $this->idProducto, "DETALLE" => $this->detalle, "ESTADO" => $this->estado, "TIEMPO ESTIMADO FINALIZACION" => $this->tiempoEstimadoFinalizacion, "ID MESA" => $this->idMesa, "COSTO TOTAL" => $this->costoTotal, "FECHA Y HORA CREACION" => $this->fechaHoraCreacion));
    }

    public static function generarIdAlfanumerico() 
    {
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $cadenaAleatoria = '';
        $maxCaracteres = strlen($caracteres) - 1;
    
        for ($i = 0; $i < 5; $i++) {
            $indiceAleatorio = rand(0, $maxCaracteres);
            $cadenaAleatoria .= $caracteres[$indiceAleatorio];
        }
    
        return $cadenaAleatoria;
    }

    public static function calcularTiempoEstimadoFinalizacion($productoComanda)
    {
        $controladorProductos = new ProductoController();
        //inicializo cantidades
        $cantidadBartender = 0;
        $cantidadCerveceros = 0;
        $cantidadMozos = 0; 
        $cantidadCocineros = 0;

        //obtengo cantidad de empleados de cada rubro
        $controladorEmpelados = new EmpleadoController();
        $arrayEmpleados = $controladorEmpelados->listarEmpleados();
        
        foreach($arrayEmpleados as $empleado)
        {
            switch($empleado->tipo)
            {
                case "bartender":
                    $cantidadBartender++;
                    break;
                case "cervecero":
                    $cantidadCerveceros++;
                    break;
                case "cocinero":
                    $cantidadCocineros++;
                    break;
                case "mozo":
                    $cantidadMozos++;
                    break;
            }
        }
        $flagPrimero = false;

            if($productoComanda->estado == "listo para servir")
            {
                $tiempo = 0;
            }
            else if($productoComanda->estado == "en preparacion")
            {
                $objetoProducto = $controladorProductos->buscarProductoPorNombre($productoComanda->detalle);
    
                    switch($objetoProducto->tipo)
                    {
                        case "trago":
                            //trago 5 minutos cada uno
                            $tiempo = 5 / $cantidadBartender;
                            break;
                        case "cerveza":
                            //cerveza 2 minutos
                            $tiempo = 2 / $cantidadCerveceros;
                            break;
                        case "comida":
                            //comida 30 minutos, base de 10 minutos porque no hay tiempos que, a pesar de la cantidad de cocineros, no se pueden acelerar
                            $tiempo = (20 / $cantidadCocineros) + 10;
                            break;
                        case "postre":
                            //postre 10 minutos, base de 5 minutos porque no hay tiempos que, a pesar de la cantidad de cocineros, no se pueden acelerar
                            $tiempo = (5 / $cantidadCocineros) + 5;
                                break;
                    }
                    //por cada mozo, tardara 5 minutos por comanda.
            
                    $tiempo += (5 / $cantidadMozos);
                    $tiempo = ceil($tiempo);
            }
                if(!$flagPrimero)
                {
                    $flagPrimero = true;
                    $stringTiempos = $tiempo;
                }
                else
                {
                    $stringTiempos .= "," . $tiempo;
                }
 
        

        
        return $stringTiempos; 
    }

    public static function calcularCostoTotal($detalle)
    {
        $controladorProductos = new ProductoController();
        $costoTotal = 0;

        $arrayProductos = explode(",", $detalle);
        foreach($arrayProductos as $producto)
        {
            $objetoProducto = $controladorProductos->buscarProductoPorNombre($producto);
            $costoTotal += $objetoProducto->precio;
        }
        return $costoTotal;
    }


    public static function ValidarDatos($detalle)
    {
        $controladorProductos = new ProductoController();
        $controladorEmpleados = new EmpleadoController();
        $retorno = 1;

        $arrayProductos = explode(",", $detalle);
        foreach($arrayProductos as $producto)
        {
            $objetoProducto = $controladorProductos->buscarProductoPorNombre($producto);
            if($objetoProducto == false)
            {
                $retorno = 0;
                break;
            }
            else
            {
                switch($objetoProducto->tipo)
                {
                    case "trago":
                        if($controladorEmpleados->listarEmpleadosPorTipo("bartender") == false)
                        {
                            $retorno = -1;
                            break;
                        }
                        break;
                    case "cerveza":
                        if($controladorEmpleados->listarEmpleadosPorTipo("cervecero") == false)
                        {
                            $retorno = -2;
                            break;
                        }
                        break;
                    case "comida":
                        if($controladorEmpleados->listarEmpleadosPorTipo("cocinero") == false)
                        {
                            $retorno = -3;
                            break;
                        }
                        break;
                    case "postre":
                        if($controladorEmpleados->listarEmpleadosPorTipo("cocinero") == false)
                        {
                            $retorno = -3;
                            break;
                        }
                        break;     
                }
            }
        }


        return $retorno;
    }

    public static function ParsearComanda($comanda)
    {
            $flagPrimero = false;
            foreach($comanda as $c)
            {
                if(!$flagPrimero)
                {
                    $flagPrimero = true;
                    $stringDetalles = $c->detalle;
                    $stringTiempos =$c->tiempoEstimadoFinalizacion;
                    $stringIdProducto = $c->idProducto;
                    $stringEstado = $c->estado;
                }
                else
                {
                    $stringDetalles .= "," . $c->detalle;
                    $stringTiempos .= "," . $c->tiempoEstimadoFinalizacion;
                    $stringIdProducto .= "," . $c->idProducto;
                    $stringEstado .= "," . $c->estado;
                }
            }
            $nuevaComanda = new Comanda();
            $nuevaComanda->id = $comanda[0]->id;
            $nuevaComanda->idCliente = $comanda[0]->idCliente;
            $nuevaComanda->nombreCliente = $comanda[0]->nombreCliente;
            $nuevaComanda->idProducto = $stringIdProducto;
            $nuevaComanda->detalle = $stringDetalles;
            $nuevaComanda->estado = $stringEstado;
            $nuevaComanda->tiempoEstimadoFinalizacion = $stringTiempos;
            $nuevaComanda->idMesa = $comanda[0]->idMesa;
            $nuevaComanda->costoTotal = $comanda[0]->costoTotal;
            $nuevaComanda->fechaHoraCreacion = $comanda[0]->fechaHoraCreacion;
    
        return $nuevaComanda;
    }
    public function InsertarLaComandaParametros()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $arrayProductos = explode(",", $this->detalle);
        $arrayTiempoEstimado = explode(",", $this->tiempoEstimadoFinalizacion);
        $arrayEstados = explode(",", $this->estado);
        $i = 0;
        foreach($arrayProductos as $producto)
        {
            $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into comandas (id,id_cliente,nombre_cliente,detalle,estado,tiempo_estimado_finalizacion,id_mesa,costo_total,fecha_hora_creacion)values(:id,:idCliente,:nombreCliente,:detalle,:estado,:tiempo_estimado_finalizacion,:id_mesa,:costo_total,:fecha_hora_creacion)");
            $consulta->bindValue(':id', $this->id, PDO::PARAM_STR);
            $consulta->bindValue(':idCliente', $this->idCliente, PDO::PARAM_STR);
            $consulta->bindValue(':nombreCliente', $this->nombreCliente, PDO::PARAM_STR);
            $consulta->bindValue(':detalle', $producto, PDO::PARAM_STR);
            $consulta->bindValue(':estado', $arrayEstados[$i], PDO::PARAM_STR);
            $consulta->bindValue(':tiempo_estimado_finalizacion', $arrayTiempoEstimado[$i], PDO::PARAM_STR);
            $consulta->bindValue(':id_mesa', $this->idMesa, PDO::PARAM_INT);
            $consulta->bindValue(':costo_total', $this->costoTotal, PDO::PARAM_STR);
            $consulta->bindValue(':fecha_hora_creacion', $this->fechaHoraCreacion, PDO::PARAM_STR);
            $consulta->execute();
            $i++;
        }
        return $this->id;
    }

    public static function TraerTodosLosProductosComanda()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select id,id_producto as idProducto,id_cliente as idCliente, nombre_cliente as nombreCliente,detalle,estado,tiempo_estimado_finalizacion as tiempoEstimadoFinalizacion,id_mesa as idMesa,costo_total as costoTotal,fecha_hora_creacion as fechaHoraCreacion from comandas where estado != 'concluida'");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "comanda");
    }

    public static function TraerTodosLosProductosComandaHistoricos()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select id,id_producto as idProducto,id_cliente as idCliente, nombre_cliente as nombreCliente,detalle,estado,tiempo_estimado_finalizacion as tiempoEstimadoFinalizacion,id_mesa as idMesa,costo_total as costoTotal,fecha_hora_creacion as fechaHoraCreacion from comandas");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "comanda");
    }

    public static function TraerTodosLosProductosComandaPorEstado($estado)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select id,id_producto as idProducto,id_cliente as idCliente, nombre_cliente as nombreCliente,detalle,estado,tiempo_estimado_finalizacion as tiempoEstimadoFinalizacion,id_mesa as idMesa,costo_total as costoTotal,fecha_hora_creacion as fechaHoraCreacion from comandas where estado =:estado");
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "comanda");
    }
    public static function TraerTodasLasComandasPorMesa($idMesa)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select id,id_cliente as idCliente, nombre_cliente as nombreCliente,id_producto as idProducto,detalle,estado,tiempo_estimado_finalizacion as tiempoEstimadoFinalizacion,id_mesa as idMesa,costo_total as costoTotal,fecha_hora_creacion as fechaHoraCreacion from comandas where estado != 'concluida' and id_mesa =:idmesa");
        $consulta->bindValue(':idmesa', $idMesa, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "comanda");
    }

    public static function TraerTodosLosProductosComandaPorTipo($tipo)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select c.id,c.id_cliente as idCliente,c.nombre_cliente as nombreCliente,c.id_producto as idProducto,c.detalle,c.estado,c.tiempo_estimado_finalizacion as tiempoEstimadoFinalizacion,c.id_mesa as idMesa,c.costo_total as costoTotal,c.fecha_hora_creacion as fechaHoraCreacion from comandas c JOIN menu m ON c.detalle = m.nombre
        WHERE c.estado != 'concluida'  AND m.tipo = :tipo");
        $consulta->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "comanda");
    }
    
    public static function TraerTodosLosProductosComandaPorTipoYEstado($tipo, $estado)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select c.id,c.id_cliente as idCliente,c.nombre_cliente as nombreCliente,c.id_producto as idProducto,c.detalle,c.estado,c.tiempo_estimado_finalizacion as tiempoEstimadoFinalizacion,c.id_mesa as idMesa,c.costo_total as costoTotal,c.fecha_hora_creacion as fechaHoraCreacion from comandas c JOIN menu m ON c.detalle = m.nombre
        WHERE c.estado =:estado  AND m.tipo = :tipo");
        $consulta->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "comanda");
    }
    
    public static function TraerUnaComandaPorId($id)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select id,id_cliente as idCliente,nombre_cliente as nombreCliente,id_producto as idProducto,detalle,estado,tiempo_estimado_finalizacion as tiempoEstimadoFinalizacion,id_mesa as idMesa,costo_total as costoTotal,fecha_hora_creacion as fechaHoraCreacion from comandas where estado != 'concluida' and id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "comanda");
    }

    public static function TraerUnaComandaConcluidaPorId($id)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select id,id_cliente as idCliente,nombre_cliente as nombreCliente,id_producto as idProducto,detalle,estado,tiempo_estimado_finalizacion as tiempoEstimadoFinalizacion,id_mesa as idMesa,costo_total as costoTotal,fecha_hora_creacion as fechaHoraCreacion from comandas where estado = 'concluida' and id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "comanda");
    }

    public function ModificarComandaParametros()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $arrayProductos = explode(",", $this->detalle);
        $arrayIdProducto = explode(",", $this->idProducto);
        $arrayTiempoEstimado = explode(",", $this->tiempoEstimadoFinalizacion);
        $arrayEstados = explode(",", $this->estado);
        $i = 0;
        foreach($arrayProductos as $producto)
        {
            $consulta = $objetoAccesoDato->RetornarConsulta("
            update comandas 
            set id=:id,
            id_cliente=:idCliente,
            nombre_cliente=:nombreCliente,
            detalle=:detalle,
            estado=:estado,
            tiempo_estimado_finalizacion=:tiempo_estimado_finalizacion,
            id_mesa=:id_mesa,
            costo_total=:costo_total,
            fecha_hora_creacion=:fecha_hora_creacion
            WHERE id_producto=:idProducto");
            $consulta->bindValue(':idProducto', $arrayIdProducto[$i], PDO::PARAM_STR);
            $consulta->bindValue(':id', $this->id, PDO::PARAM_STR);
            $consulta->bindValue(':idCliente', $this->idCliente, PDO::PARAM_INT);
            $consulta->bindValue(':nombreCliente', $this->nombreCliente, PDO::PARAM_STR);
            $consulta->bindValue(':detalle', $producto, PDO::PARAM_STR);
            $consulta->bindValue(':estado', $arrayEstados[$i], PDO::PARAM_STR);
            $consulta->bindValue(':tiempo_estimado_finalizacion', $arrayTiempoEstimado[$i], PDO::PARAM_STR);
            $consulta->bindValue(':id_mesa', $this->idMesa, PDO::PARAM_INT);
            $consulta->bindValue(':costo_total', $this->costoTotal, PDO::PARAM_STR);
            $consulta->bindValue(':fecha_hora_creacion', $this->fechaHoraCreacion, PDO::PARAM_STR);
            $i++;
            $resultado = $consulta->execute();
        }
        return $resultado;
    }

    public function PreparacionProductoComanda()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

        $consulta = $objetoAccesoDato->RetornarConsulta("
        update comandas 
        set estado=:estado,
        tiempo_estimado_finalizacion=:tiempoEstimadoFinalizacion
        WHERE id_producto=:idProducto");
        $consulta->bindValue(':idProducto', $this->idProducto, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoEstimadoFinalizacion', $this->tiempoEstimadoFinalizacion, PDO::PARAM_STR);
        $resultado = $consulta->execute();
        return $resultado;
    }

    public static function TraerProductoComanda($idProducto)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select id,id_cliente as idCliente,nombre_cliente as nombreCliente,id_producto as idProducto,detalle,estado,tiempo_estimado_finalizacion as tiempoEstimadoFinalizacion,id_mesa as idMesa,costo_total as costoTotal,fecha_hora_creacion as fechaHoraCreacion from comandas where estado != 'concluida' and id_producto = :idProducto");
        $consulta->bindValue(':idProducto', $idProducto, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchObject("comanda");
    }
}
?>