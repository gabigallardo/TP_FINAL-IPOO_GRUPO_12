<?php
class Viaje
{
    private $codigo;
    private $destino;
    private $maxPasajeros;
    private $pasajerosArray;
    private $objResponsable;
    private $costo;
    private $costosAbonados;
    private $objEmpresa;
    private $mensajeoperacion;

    public function __construct()
    {
        $this->codigo = "";
        $this->destino = "";
        $this->maxPasajeros = "";
        $this->pasajerosArray = [];
        $this->objResponsable = null;
        $this->costo = "";
        $this->costosAbonados = 0;
        $this->objEmpresa = null;
    }
    public function cargar($cod, $dest, $maxPas, $objEmpresa, $resp, $costo)
    {
        $this->setCodigo($cod);
        $this->setDestino($dest);
        $this->setMaxPasajeros($maxPas);
        $this->setObjEmpresa($objEmpresa);
        $this->setObjResponsable($resp);
        $this->setCosto($costo);
    }
    // Metodos de acceso (getters)
    public function getCodigo()
    {
        return $this->codigo;
    }

    public function getDestino()
    {
        return $this->destino;
    }

    public function getMaxPasajeros()
    {
        return $this->maxPasajeros;
    }

    public function getPasajerosArray()
    {
        return $this->pasajerosArray;
    }

    public function getObjResponsable()
    {
        return $this->objResponsable;
    }
    public function getCosto()
    {
        return $this->costo;
    }
    public function getCostosAbonados()
    {
        return $this->costosAbonados;
    }
    public function getObjEmpresa()
    {
        return $this->objEmpresa;
    }
    public function getmensajeoperacion()
    {
        return $this->mensajeoperacion;
    }
    // Metodos de modificacion (setters)
    public function setCostosAbonados($costos)
    {
        $this->costosAbonados = $costos;
    }
    public function setCosto($costo)
    {
        $this->costo = $costo;
    }
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }

    public function setDestino($destino)
    {
        $this->destino = $destino;
    }

    public function setMaxPasajeros($maxPasajeros)
    {
        $this->maxPasajeros = $maxPasajeros;
    }

    public function setPasajerosArray($pasajeros)
    {
        $this->pasajerosArray = $pasajeros;
    }

    public function setObjResponsable($responsable)
    {
        $this->objResponsable = $responsable;
    }
    public function setObjEmpresa($objEmpresa)
    {
        $this->objEmpresa = $objEmpresa;
    }
    public function setmensajeoperacion($mensajeoperacion)
    {
        $this->mensajeoperacion = $mensajeoperacion;
    }

    private function getStringArray($array)
    {
        $cadena = "";
        foreach ($array as $elemento) {
            $cadena = $cadena . " " . $elemento . "\n";
        }
        return $cadena;
    }

    public function Buscar($id)
    {
        $base = new BaseDatos();
        $consultaviaje = "SELECT * FROM viaje WHERE idviaje=" . $id;
        $resp = false;
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaviaje)) {
                if ($row2 = $base->Registro()) {
                    $this->setCodigo($id);
                    $this->setDestino($row2['vdestino']);
                    $this->setMaxPasajeros($row2['vmaxpasajeros']);

                    $empresa = new Empresa();
                    $empresa->Buscar($row2['idempresa']);
                    $this->setObjEmpresa($empresa);


                    $this->setObjResponsable($row2['rnumeroempleado']);;


                    $this->setCosto($row2['vimporte']);

                    $pasajero = new Pasajero();
                    $this->setPasajerosArray($pasajero->listar("idviaje = " . $id));
                    $resp = true;
                }
            } else {
                $this->setmensajeoperacion($base->getError());
            }
        } else {
            $this->setmensajeoperacion($base->getError());
        }
        return $resp;
    }

    public function listar($condicion = "")
    {
        $arregloviaje = null;
        $base = new BaseDatos();
        $consultaviajes = "SELECT * FROM viaje";
        if ($condicion != "") {
            $consultaviajes .= ' WHERE ' . $condicion;
        }
        $consultaviajes .= " ORDER BY idviaje";

        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaviajes)) {
                $arregloviaje = array();
                while ($row2 = $base->Registro()) {
                    $viaje = new Viaje();

                    $empresa = new Empresa();
                    $empresa->Buscar($row2['idempresa']);

                    $responsable = new ResponsableV();
                    $responsable->Buscar($row2['rnumeroempleado']);


                    $viaje->cargar(
                        $row2['idviaje'],
                        $row2['vdestino'],
                        $row2['vmaxpasajeros'],
                        $empresa,
                        $responsable,
                        $row2['vimporte']
                    );

                    // Recuperar los pasajeros del viaje
                    $pasajero = new Pasajero();
                    $pasajeros = $pasajero->listar("idviaje = " . $row2['idviaje']);
                    $viaje->setPasajerosArray($pasajeros);

                    array_push($arregloviaje, $viaje);
                }
            } else {
                $this->setmensajeoperacion($base->getError());
            }
        } else {
            $this->setmensajeoperacion($base->getError());
        }
        return $arregloviaje;
    }


    public function insertar()
    {
        $base = new BaseDatos();
        $resp = false;

        $destino = $this->getDestino();
        $cantMaxPasajeros = $this->getMaxPasajeros();
        $idEmpresa = $this->getObjEmpresa()->getId();
        $numEmpleado = $this->getObjResponsable()->getNumeroEmpleado();
        $importe = $this->getCosto();

        $consultaInsertar = "INSERT INTO viaje(vdestino, vmaxpasajeros, idempresa, rnumeroempleado, vimporte) VALUES ('{$destino}','{$cantMaxPasajeros}','{$idEmpresa}','{$numEmpleado}','{$importe}')";


        if ($base->Iniciar()) {

            if ($idViaje = $base->devuelveIDInsercion($consultaInsertar)) {
                $this->setCodigo($idViaje);
                $resp =  true;
            } else {
                $this->setmensajeoperacion($base->getError());
            }
        } else {
            $this->setmensajeoperacion($base->getError());
        }
        return $resp;
    }


    public function modificar()
    {
        $resp = false;
        $base = new BaseDatos();

        $destino = $this->getDestino();
        $cantMaxPasajeros = $this->getMaxPasajeros();
        $idEmpresa = $this->getObjEmpresa();
        $numEmpleado = $this->getObjResponsable();
        $importe = $this->getCosto();

        $consultaModifica = "UPDATE viaje 
                     SET vdestino='{$destino}', 
                         vmaxpasajeros='{$cantMaxPasajeros}', 
                         idempresa='{$idEmpresa}', 
                         rnumeroempleado='{$numEmpleado}', 
                         vimporte='{$importe}' 
                     WHERE idviaje={$this->getCodigo()}";


        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaModifica)) {
                $resp =  true;
            } else {
                $this->setmensajeoperacion($base->getError());
            }
        } else {
            $this->setmensajeoperacion($base->getError());
        }
        return $resp;
    }

    public function eliminar()
    {
        $base = new BaseDatos();
        $resp = false;
        if ($base->Iniciar()) {
            $consultaBorra = "DELETE FROM viaje WHERE idviaje=" . $this->getCodigo();
            if ($base->Ejecutar($consultaBorra)) {
                $resp =  true;
            } else {
                $this->setmensajeoperacion($base->getError());
            }
        } else {
            $this->setmensajeoperacion($base->getError());
        }
        return $resp;
    }

    public function __toString()
    {
        $pasajeros = $this->getStringArray($this->getPasajerosArray());
        $info = "Codigo de Viaje: {$this->getCodigo()}\n";
        $info .= "Destino: {$this->getDestino()}\n";
        $info .= "Cantidad Maxima de Pasajeros: {$this->getMaxPasajeros()}\n";
        $info .= "Empresa: {$this->getObjEmpresa()}\n";
        $info .= "Responsable del viaje: {$this->getObjResponsable()} \n";
        $info .= "El costo del viaje es: {$this->getCosto()}\n";
        $info .= "Pasajeros: {$pasajeros}\n";
        return $info;
    }
}
