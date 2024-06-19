<?php
class Persona
{
	private $nrodoc;
	private $nombre;
	private $apellido;
	private $telefono;
	private $mensajeoperacion;


	public function __construct()
	{

		$this->nrodoc = "";
		$this->nombre = "";
		$this->apellido = "";
		$this->telefono = "";
	}

	public function cargar($param)
	{
		$this->setNrodoc($param['nrodoc']);
		$this->setNombre($param['nombre']);
		$this->setApellido($param['apellido']);
		$this->setTelefono($param['telefono']);
	}


	public function setNrodoc($NroDNI)
	{
		$this->nrodoc = $NroDNI;
	}
	public function setNombre($Nom)
	{
		$this->nombre = $Nom;
	}
	public function setApellido($Ape)
	{
		$this->apellido = $Ape;
	}
	public function setTelefono($Tel)
	{
		$this->telefono = $Tel;
	}

	public function setmensajeoperacion($mensajeoperacion)
	{
		$this->mensajeoperacion = $mensajeoperacion;
	}

	public function getNrodoc()
	{
		return $this->nrodoc;
	}
	public function getNombre()
	{
		return $this->nombre;
	}
	public function getApellido()
	{
		return $this->apellido;
	}
	public function getTelefono()
	{
		return $this->telefono;
	}


	public function getmensajeoperacion()
	{
		return $this->mensajeoperacion;
	}

	/**
	 * Recupera los datos de una persona por dni
	 * @param int $dni
	 * @return true en caso de encontrar los datos, false en caso contrario 
	 */
	public function Buscar($dni)
	{
		$base = new BaseDatos();
		$consultaPersona = "Select * from persona where pnumdoc=" . $dni;
		$resp = false;
		if ($base->Iniciar()) {
			if ($base->Ejecutar($consultaPersona)) {
				if ($row2 = $base->Registro()) {
					$this->setNrodoc($dni);
					$this->setNombre($row2['pnombre']);
					$this->setApellido($row2['papellido']);
					$this->setTelefono($row2['ptelefono']);
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
		$arregloPersona = null;
		$base = new BaseDatos();
		$consultaPersonas = "Select * from persona ";
		if ($condicion != "") {
			$consultaPersonas = $consultaPersonas . ' where ' . $condicion;
		}
		$consultaPersonas .= " order by papellido ";
		if ($base->Iniciar()) {
			if ($base->Ejecutar($consultaPersonas)) {
				$arregloPersona = array();
				while ($row2 = $base->Registro()) {

					$NroDoc = $row2['pnumdoc'];
					$Nombre = $row2['pnombre'];
					$Apellido = $row2['papellido'];
					$telefono = $row2['ptelefono'];

					$perso = new Persona();
					$perso->cargar($NroDoc, $Nombre, $Apellido, $telefono);
					array_push($arregloPersona, $perso);
				}
			} else {
				$this->setmensajeoperacion($base->getError());
			}
		} else {
			$this->setmensajeoperacion($base->getError());
		}
		return $arregloPersona;
	}

	public function insertar()
	{
		$base = new BaseDatos();
		$resp = false;


		$consultaInsertar = "INSERT INTO persona(pnumdoc, pnombre, papellido, ptelefono) 
			VALUES (" . $this->getNrodoc() . ", '" . $this->getNombre() . "', '" . $this->getApellido() . "', '" . $this->getTelefono() . "')";


		if ($base->Iniciar()) {

			if ($base->Ejecutar($consultaInsertar)) {

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


		$consultaModifica = "UPDATE persona SET papellido='{$this->getApellido()}',pnombre='{$this->getNombre()}', ptelefono='{$this->getTelefono()}'WHERE  pnumdoc='{$this->getNrodoc()}'";

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
			$consultaBorra = "DELETE FROM persona WHERE pnumdoc=" . $this->getNrodoc();
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
		return "\nNombre: " . $this->getNombre() . "\nApellido:" . $this->getApellido() . "\nDNI: " . $this->getNrodoc() . "\nTelefono: " . $this->getTelefono() . "\n";
	}
}
