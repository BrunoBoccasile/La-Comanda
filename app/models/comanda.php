<?php
include_once __DIR__ . "/../controladores/productoController.php";
include_once __DIR__ . "/../controladores/empleadoController.php";

class Comanda
{
    public $id;//alfanumerico 5 caracteres
    public $detalle;//array asociativo ej:(trago => 1, cerveza => 3, comida => 4, postre => 2)
    public $estado;//pendiente/en preparacion/listo para servir
    public $tiempoEstimadoFinalizacion;//varia segun cantidad de empleados
    public $idMesa;
    public $costoTotal;
    public $fechaHoraCreacion;

    public function mostrarDatos()
    {
        return $this->id . "  " . $this->detalle . "  " . $this->estado . "  " . $this->tiempoEstimadoFinalizacion . " " . $this->idMesa . " " . $this->costoTotal . " " . $this->fechaHoraCreacion;
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

    public static function calcularTiempoEstimadoFinalizacion($detalle)
    {
        $controladorProductos = new ProductoController();
        $arrayProductos = explode(",", $detalle);
        $arrayTragos = array();
        $arrayCervezas = array();
        $arrayComidas = array();
        $arrayPostres = array();
        foreach($arrayProductos as $producto)
        {
            $objetoProducto = $controladorProductos->buscarProductoPorNombre($producto);
            switch($objetoProducto->tipo)
            {
                case "trago":
                    array_push($arrayTragos, $producto);
                    break;
                case "cerveza":
                    array_push($arrayCervezas, $producto);
                    break;
                case "comida":
                    array_push($arrayComidas, $producto);
                    break;
                case "postre":
                    array_push($arrayPostres, $producto);
                    break;
            }
        }

        //inicializo cantidades
        $cantidadBartender = 0;
        $cantidadCerveceros = 0;
        $cantidadCocineros = 0;
        $cantidadMozos = 0; 

        $tiempoTrago = 0;
        $tiempoCerveza = 0;
        $tiempoComidas = 0;
        $tiempoPostres = 0;
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
        //trago 5 minutos cada uno
        if(count($arrayTragos) > 0)
        {
            $tiempoTrago = (count($arrayTragos) * 5) / $cantidadBartender;
        }
        
        //cerveza 2 minutos por cada ronda (ronda maxima 4 unidades),
        
        $cantidadCervezas = count($arrayCervezas);
        if($cantidadCervezas > 0)
        {
            if ($cantidadCervezas <= 4) 
            {
                $tiempoCerveza = 2;
            } 
            else 
            {
                $tiempoCerveza = ceil($cantidadCervezas / 4) * 2;
            }
            
            $tiempoCerveza = $tiempoCerveza / $cantidadCerveceros;
        }
        
        //comida 30 minutos por cada 5 platos (valor arbitrario suponiendo cantidad X de cocinas y que todos los platos sean distintos)
        $cantidadComidas = count($arrayComidas);
        
        if($cantidadComidas > 0)
        {
            if ($cantidadComidas <= 5) 
            {
                $tiempoComidas = 30;
            } 
            else 
            {
                $tiempoComidas = ceil($cantidadComidas / 5) * 30;
            }
            $tiempoComidas = $tiempoComidas / $cantidadCocineros;
        }
        
        //postre 10 minutos por cada 5 postres (valor arbitrario suponiendo cantidad X de cocinas y que todos los postres sean distintos)
        $cantidadPostres = count($arrayPostres);

        if($cantidadPostres > 0)
        {
            if($cantidadPostres <= 5)
            {
                $tiempoPostres = 10;
            }
            else
            {
                $tiempoPostres = ceil($tiempoPostres / 5) * 10;
            }
    
            $tiempoPostres = $tiempoPostres / $cantidadCocineros;
        }

        $tiempoTotal = max($tiempoTrago, $tiempoCerveza, $tiempoComidas, $tiempoPostres);

        //por cada mozo, tardara 2 minutos por comanda.
        if($cantidadMozos > 0)
        {
            $tiempoTotal = $tiempoTotal + (2 / $cantidadMozos);
        }
        return $tiempoTotal; 
    }

    public static function calcularCostoTotal($detalle)
    {
        $controladorProductos = new ProductoController();
        $arrayProductos = explode(",", $detalle);
        $arrayTragos = array();
        $arrayCervezas = array();
        $arrayComidas = array();
        $arrayPostres = array();
        $costoTotal = 0;
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
        return $retorno;
    }

    public function InsertarLaComanda()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into comandas (id,detalle,estado,tiempo_estimado_finalizacion,id_mesa,costo_total,fecha_hora_creacion)values('$this->id','$this->detalle','$this->estado','$this->tiempoEstimadoFinalizacion','$this->idMesa','$this->costoTotal','$this->fechaHoraCreacion')");
        $consulta->execute();
        return $this->id;
    }

    public function InsertarLaComandaParametros()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into comandas (id,detalle,estado,tiempo_estimado_finalizacion,id_mesa,costo_total,fecha_hora_creacion)values(:id,:detalle,:estado,:tiempo_estimado_finalizacion,:id_mesa,:costo_total,:fecha_hora_creacion)");
        $consulta->bindValue(':id', $this->id, PDO::PARAM_STR);
        $consulta->bindValue(':detalle', $this->detalle, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':tiempo_estimado_finalizacion', $this->tiempoEstimadoFinalizacion, PDO::PARAM_STR);
        $consulta->bindValue(':id_mesa', $this->idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':costo_total', $this->costoTotal, PDO::PARAM_STR);
        $consulta->bindValue(':fecha_hora_creacion', $this->fechaHoraCreacion, PDO::PARAM_STR);
        $consulta->execute();
        return $this->id;
    }

    public static function TraerTodasLasComandas()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select id,detalle,estado,tiempo_estimado_finalizacion as tiempoEstimadoFinalizacion,id_mesa as idMesa,costo_total as costoTotal,fecha_hora_creacion as fechaHoraCreacion from comandas where estado != 'concluida'");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "comanda");
    }

    public static function TraerTodasLasComandasPorMesa($idMesa)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select id,detalle,estado,tiempo_estimado_finalizacion as tiempoEstimadoFinalizacion,id_mesa as idMesa,costo_total as costoTotal,fecha_hora_creacion as fechaHoraCreacion from comandas where estado != 'concluida' and id_mesa =:idmesa");
        $consulta->bindValue(':idmesa', $idMesa, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "comanda");
    }

    public static function TraerUnaComanda($id)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select id,detalle,estado,tiempo_estimado_finalizacion as tiempoEstimadoFinalizacion,id_mesa as idMesa,costo_total as costoTotal,fecha_hora_creacion as fechaHoraCreacion from comandas where id=:id and estado != 'concluida'");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();
        $comandaBuscada = $consulta->fetchObject('comanda');
        return $comandaBuscada;
    }

    public function ModificarComanda()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
				update comandas 
				set detalle='$this->detalle',
				estado='$this->estado',
				tiempo_estimado_finalizacion='$this->tiempoEstimadoFinalizacion',
                id_mesa='$this->idMesa',
                costo_total='$this->costoTotal',
                fecha_hora_creacion='$this->fechaHoraCreacion'
                WHERE id='$this->id'");
        return $consulta->execute();
    }

    public function ModificarComandaParametros()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
				update comandas 
				set detalle=:detalle,
				estado=:estado,
				tiempo_estimado_finalizacion=:tiempo_estimado_finalizacion,
                id_mesa=:id_mesa,
                costo_total=:costo_total,
                fecha_hora_creacion=:fecha_hora_creacion
                WHERE id=:id");
        $consulta->bindValue(':id', $this->id, PDO::PARAM_STR);
        $consulta->bindValue(':detalle', $this->detalle, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':tiempo_estimado_finalizacion', $this->tiempoEstimadoFinalizacion, PDO::PARAM_STR);
        $consulta->bindValue(':id_mesa', $this->idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':costo_total', $this->costoTotal, PDO::PARAM_STR);
        $consulta->bindValue(':fecha_hora_creacion', $this->fechaHoraCreacion, PDO::PARAM_STR);
        return $consulta->execute();
    }

    public function BorrarComanda()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
				delete 
				from comandas				
				WHERE id=:id");
        $consulta->bindValue(':id', $this->id, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->rowCount();
    }
}
?>