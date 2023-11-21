<?php

class ManejadorArchivos
{
    private $urlArchivo;

    public function __construct($urlArchivo) 
    {
        $this->urlArchivo = $urlArchivo;
    }

    public function leer() 
    {
        if (file_exists($this->urlArchivo)) 
        {
            $archivo = fopen($this->urlArchivo, 'r');
            $data = [];

            while (($fila = fgetcsv($archivo)) !== false) 
            {
                array_push($data, $fila);            
            }

            fclose($archivo);

            return $data;
        } 
        else 
        {
            return [];
        }
    }

    public function guardar($data) 
    {
        $file = fopen($this->urlArchivo, 'w');

        foreach ($data as $fila) 
        {
            fputcsv($file, $fila);
        }

        fclose($file);
    }
}

?>