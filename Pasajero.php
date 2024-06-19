<?php

class Pasajero extends Persona
{
	private $idPasajero;
	private $numAsiento;
	private $numTicket;
	private $objViaje;
	private $mensajeoperacion;

	public function __construct()
	{
		parent::__construct();
		$this->idPasajero = "";
		$this->numAsiento = "";
		$this->numTicket = "";
		$this->objViaje = null;
	}
	public function cargar($param)
	{
		parent::cargar($param);
		$this->setIdPasajero($param['idPasajero']);
		$this->setNumAsiento($param['numAsiento']);
		$this->setNumTicket($param['numTicket']);
		$this->setObjViaje($param['objViaje']);
	}

	public function getIdPasajero()
	{
		return $this->idPasajero;
	}
	public function getNumAsiento()
	{
		return $this->numAsiento;
	}
	public function getNumTicket()
	{
		return $this->numTicket;
	}
	public function getObjViaje()
	{
		return $this->objViaje;
	}
	public function getmensajeoperacion()
	{
		return $this->mensajeoperacion;
	}
	public function setIdPasajero($IdPas)
	{
		$this->idPasajero = $IdPas;
	}
	public function setNumAsiento($numAsiento)
	{
		$this->numAsiento = $numAsiento;
	}
	public function setNumTicket($numTicket)
	{
		$this->numTicket = $numTicket;
	}
	public function setObjViaje($objViaje)
	{
		$this->objViaje = $objViaje;
	}
	public function setmensajeoperacion($mensajeoperacion)
	{
		$this->mensajeoperacion = $mensajeoperacion;
	}
	public function Buscar($dni)
	{
		$base = new BaseDatos();
		$consulta = "Select * from pasajero where pdocumento=" . $dni;
		$resp = false;
		if ($base->Iniciar()) {
			if ($base->Ejecutar($consulta)) {
				if ($row2 = $base->Registro()) {
					parent::Buscar($dni);
					$this->setIdPasajero($row2['idpasajero']);
					$this->setNumAsiento($row2['numasiento']);
					$this->setNumTicket($row2['numticket']);
					$this->setObjViaje($row2['idviaje']);
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
		$arreglo = null;
		$base = new BaseDatos();
		$consulta = "Select * from pasajero ";
		if ($condicion != "") {
			$consulta = $consulta . ' where ' . $condicion;
		}
		$consulta .= " order by idpasajero ";

		if ($base->Iniciar()) {
			if ($base->Ejecutar($consulta)) {
				$arreglo = array();
				while ($row2 = $base->Registro()) {
					$obj = new Pasajero();
					$obj->Buscar($row2['pdocumento']);
					array_push($arreglo, $obj);
				}
			} else {
				$this->setmensajeoperacion($base->getError());
			}
		} else {
			$this->setmensajeoperacion($base->getError());
		}
		return $arreglo;
	}
	public function insertar()
	{
		$base = new BaseDatos();
		$resp = false;
		$numDocPasajero = parent::getNrodoc();
		if (parent::insertar()) {


			$consultaInsertar = "INSERT INTO pasajero(pdocumento, numasiento, numticket, idviaje)
		VALUES ('{$numDocPasajero}', '{$this->getNumAsiento()}','{$this->getNumTicket()}' ,'{$this->getObjViaje()->getCodigo()}')";

			if ($base->Iniciar()) {
				if ($idpasajero = $base->devuelveIDInsercion($consultaInsertar)) {
					$this->setIdPasajero($idpasajero);
					$resp =  true;
				} else {
					$this->setmensajeoperacion($base->getError());
				}
			} else {
				$this->setmensajeoperacion($base->getError());
			}
		}
		return $resp;
	}



	public function modificar()
	{
		$resp = false;
		$base = new BaseDatos();
		if (parent::modificar()) {
			$consultaModifica = "UPDATE pasajero SET numasiento=" . $this->getNumAsiento() . ",numticket = " . $this->getNumTicket() . ",idviaje=" . $this->getObjViaje() . " WHERE pdocumento=" . $this->getNrodoc();
			if ($base->Iniciar()) {
				if ($base->Ejecutar($consultaModifica)) {
					$resp =  true;
				} else {
					$this->setmensajeoperacion($base->getError());
				}
			} else {
				$this->setmensajeoperacion($base->getError());
			}
		}

		return $resp;
	}

	public function eliminar()
	{
		$base = new BaseDatos();
		$resp = false;
		if ($base->Iniciar()) {
			$consultaBorra = "DELETE FROM pasajero WHERE pdocumento=" . $this->getNrodoc();
			if ($base->Ejecutar($consultaBorra)) {
				if (parent::eliminar()) {
					$resp =  true;
				}
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
		$info = parent::__toString();
		$info .= "Numero de asiento:{$this->getNumAsiento()}\nNumero de ticket:{$this->getNumTicket()} \n";
		return $info;
	}
}
