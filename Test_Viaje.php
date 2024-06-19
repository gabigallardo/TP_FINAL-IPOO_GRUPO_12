<?php
include_once 'Persona.php';
include_once 'Pasajero.php';
include_once 'ResponsableV.php';
include_once 'Empresa.php';
include_once 'Viaje.php';
include_once 'BaseDatos.php';

$bandera = false;
while (!$bandera) {
    echo "\nMenu:\n";
    echo "0. Salir\n";
    echo "1. Cargar informacion del viaje\n";
    echo "2. Modificar informacion del viaje\n";
    echo "3. Eliminar informacion del viaje\n";
    echo "4. Ver datos del viaje\n";
    echo "5. Cargar informacion de la empresa\n";
    echo "6. Modificar informacion de la empresa\n";
    echo "7. Eliminar informacion de la empresa\n";
    echo "8. Agregar Pasajero\n";
    echo "9. Modificar Pasajero\n";
    echo "10. Eliminar Pasajero\n";

    $opcion = readline("Ingrese la opcion deseada: ");
    if ($opcion >= 0 && $opcion <= 10) {
        switch ($opcion) {
            case '0':
                echo "Saliendo del programa...\n";
                $bandera = true;
                break;
            case '1':
                cargarViaje();
                break;
            case '2':
                modificarViaje();
                break;
            case '3':
                eliminarViaje();
                break;
            case '4':
                verDatosViaje();
                break;
            case '5':
                cargarEmpresa();
                break;
            case '6':
                $empresa = new Empresa();
                $empresas = $empresa->listar();
                foreach($empresas as $empresa){
                    echo $empresa;
                }
                $idEmpresa = readline("Ingrese el id de la empresa a modificar: ");
                $empresa->Buscar($idEmpresa);
                menuModificarEmpresa($empresa);
                break;
            case '7':
                eliminarEmpresa();
                break;
            case '8':
                agregarPasajero();
                break;
            case '9':
                modificarPasajero();
                break;
            case '10':
                eliminarPasajero();
                break;
            default:
                echo "Opcion invalida. Por favor, seleccione una opcion valida.\n";
                break;
        }
    } else {
        echo "Ingrese un numero del 0 al 9";
    }
}

function cargarViaje()
{
    $empresa = new Empresa;
    $empresasCargadas = $empresa->listar();
    if ($empresasCargadas == null) {
        echo "No hay una empresa cargada, por favor cargue una empresa antes\n";
    }else{ 
    foreach ($empresasCargadas as $empresa) {
        echo $empresa;
    } 
   $idEmpresa = readline("Ingrese el id de la empresa a cargar el viaje: ");
   $empresa->Buscar($idEmpresa);
   $nuevoResponsable = new ResponsableV();
   $persona = new Persona();
   //Validacion de DNI de RESPONSABLE
   do {
       $nroDocResponsableV = readline("Ingrese el numero de documento del empleado responsable del nuevo viaje: ");
       if (!is_numeric($nroDocResponsableV) || $nroDocResponsableV <= 0) {
           echo "El numero de documento NO puede ser una letra y debe ser mayor a 0.\n";
       }
   } while (!is_numeric($nroDocResponsableV) || $nroDocResponsableV <= 0);
   $encontrado = $persona->Buscar($nroDocResponsableV);
   //Validacion de existencia de una persona cargada con ese documento. Utilizamos la clase persona porque puede existir un pasajero con ese o un responsable con ese mismo documento
   while ($encontrado == true) {
       $nroDocResponsableV = readline("Esta persona ya ha sido cargada, por favor ingrese otro dni: \n");
       $encontrado = $persona->Buscar($nroDocResponsableV);
   }
     //Validacion de Nombre y Apellido de RESPONSABLE
    do {
        $nombreResponsableV = readline("Ingrese el nombre del responsable del nuevo viaje: ");
        $apellidoResponsableV = readline("Ingrese el apellido del responsable del nuevo viaje: ");
        if ($nombreResponsableV == ""  || $apellidoResponsableV == "") {
            echo "--->Los datos no pueden ser vacios.<---\n";
        }
    } while ($nombreResponsableV == ""  || $apellidoResponsableV == "");
 
    //Validacion de telefono de RESPONSABLE
    do {
        $telefonoResponsableV = readline("Ingrese el telefono del responsable del nuevo viaje: ");
        if (!is_numeric($telefonoResponsableV) || $telefonoResponsableV <= 0) {
            echo "El telefono debe ser un numero entero positivo.\n";
        }
    } while (!is_numeric($telefonoResponsableV) || $telefonoResponsableV <= 0);

    //Validacion de NUM LICENCIA de RESPONSABLE
    do {
        $numLicencia = readline("Ingrese el numero de licencia del responsable del nuevo viaje: ");
        if (!is_numeric($numLicencia) || $numLicencia <= 0) {
            echo "El numero de licencia debe ser un numero entero positivo.\n";
        }
    } while (!is_numeric($numLicencia) || $numLicencia <= 0);
 
    $paramNuevoResponsable = [
        'nrodoc' => $nroDocResponsableV,
        'nombre' => $nombreResponsableV,
        'apellido' => $apellidoResponsableV,
        'telefono' => $telefonoResponsableV,
        'numeroEmpleado' => null,
        'rnumerolicencia' => $numLicencia
    ];
   }   

   $nuevoResponsable->cargar($paramNuevoResponsable);
   $nuevoResponsable->insertar();

   //Verificacion de Viaje
   do {
       $maxPasajeros = readline("Ingrese el numero maximo de pasajeros: ");
       $costoDelViaje = readline("Ingrese el costo del viaje: ");
       $destino = readline("Ingrese el destino: ");

       if ($maxPasajeros < 1) {
           echo "El numero maximo de pasajeros debe ser mayor a 0.\n";
       }

       if ($costoDelViaje <= 0) {
           echo "El costo del viaje debe ser mayor a 0.\n";
       }

       if ($destino == "") {
           echo "El destino no puede estar vacio o  ser un numero.\n";
       }
   } while ($maxPasajeros <= 1 || $costoDelViaje <= 0 || $destino == "" || is_int($destino));

   $viaje = new Viaje();

       $viaje->cargar(null, $destino, $maxPasajeros, $empresa, $nuevoResponsable, $costoDelViaje);
   if($viaje->insertar()){
       echo "El viaje ha sido agregado exitosamente\n";

}
}

function modificarViaje()
{
    $bandera = false;
    $viaje = new Viaje();
    $infoViaje = $viaje->listar();
    foreach ($infoViaje as $viaje) {
        echo "\n{$viaje}\n";
        echo "-------\n";
    }
    if (count($infoViaje) > 0) {
        while (!$bandera) {
            echo "\nDesea modificar:\n";
            echo "1. Modificar destino del viaje\n";
            echo "2. Modificar maximo de pasajeros del viaje\n";
            echo "3. Modificar responsable del viaje\n";
            echo "4. Modificar costos del viaje\n";
            echo "5. Volver al menu principal\n";
            $opcion = readline("Ingrese la opcion deseada: ");
            if ($opcion == 5) {
                echo "Regresando al menu principal...\n";
                $bandera = true;
            } elseif ($opcion <= 4 && $opcion >= 1) {
                $idAModificar = readline("Ingrese el ID del viaje a modificar: ");
                if ($idAModificar >= 1 && $viaje->Buscar($idAModificar)) {
                    switch ($opcion) {
                        case '1':
                            modificarDestino($viaje);
                            break;
                        case '2':
                            modificarMaxPasajeros($viaje);
                            break;
                        case '3':
                            modificarResponsable($viaje);
                            break;
                        case '4':
                            modificarCosto($viaje);
                            break;
                        default:
                            break;
                    }
                } else {
                    echo "\nEl viaje no ha podido ser encontrado o no se ingreso un numero.\n";
                }
            } else {
                echo "Opcion invalida. Por favor, seleccione una opcion del 1 al 6.\n";
            }
        }
    } else {
        echo "\nNo hay viajes para modificar.\n";
    }
}

function eliminarViaje()
{
    $viaje = new Viaje();
    $infoViaje = $viaje->listar();
    foreach ($infoViaje as $viaje) {
        echo "\n{$viaje}\n";
        echo "-------\n";
    }
    if (count($infoViaje) > 0) {
        echo "Advertencia, al eliminar el viaje se eliminarán todos los pasajeros y el responsable del viaje\n";
        echo "\nIngrese el codigo del viaje a eliminar? ";
        $rta = trim(fgets(STDIN));

        if ($rta != null && $viaje->Buscar($rta)) {
            $pasajero= new Pasajero();
            $arrayPasajerosViaje = $pasajero->listar("idviaje =". $rta);
            if(count($arrayPasajerosViaje)>0){
                foreach($arrayPasajerosViaje as $pasajero){
                $pasajero->eliminar();}
            }
            $responsable = new ResponsableV ();
            $numEmpleado = $viaje->getObjResponsable();
            $viaje->Eliminar();
            $responsable->Buscar($numEmpleado);
            $responsable->eliminar();
        } else {
            echo "\nEl viaje que quiere eliminar no existe o ingreso una opcion incorrecta.\n";
        }
    } else {
        echo "\nNo hay viajes para eliminar.\n";
    }
}

function modificarDestino($viaje)
{
    echo "Este es el Destino actual del viaje: " . $viaje->getDestino() . "\n";
    $viaje->setDestino(readline("Ingrese el destino del viaje: "));
    $viaje->modificar();
}

function modificarMaxPasajeros($viaje)
{
    echo "Esta es la cantidad maxima actual del viaje: " . $viaje->getMaxPasajeros() . "\n";
    $viaje->setMaxPasajeros(readline("Ingrese la cantidad maxima de pasajeros del viaje: "));
    $viaje->modificar();
}

function modificarResponsable($viaje)
{
    echo "Que informacion desea modificar del responsable del viaje?\n";
    echo "1- El numero de licencia\n";
    echo "2- El nombre\n";
    echo "3- El apellido\n";
    echo "4- Todos los datos\n";
    echo "5- Volver\n";
    $opcion = trim(fgets(STDIN));
    modificarResponsableViaje($viaje, $opcion);
}

function modificarCosto($viaje)
{
    echo "Este es el Costo actual del viaje: " . $viaje->getCosto() . "\n";
    $viaje->setCosto(readline("Ingrese el nuevo costo del viaje: "));
    $viaje->modificar();
}

function menuModificarEmpresa($empresaAModificar)
{
    echo "Que informacion desea modificar de la empresa?\n";
    echo "1- El nombre\n";
    echo "2- La direccion\n";
    echo "3- Todos los datos\n";
    $eleccion = trim(fgets(STDIN));
    modificarEmpresaDatos($empresaAModificar, $eleccion);
}

function verDatosViaje()
{
    $objViaje = new Viaje();
    $viajes = $objViaje->listar();
    foreach ($viajes as $viaje) {
        echo "\nID viaje: {$viaje->getCodigo()} \n";
        echo "Destino viaje: {$viaje->getDestino()} \n";
        echo "-------\n";
    }
    echo "\nIngrese un id: ";
    $idViaje = trim(fgets(STDIN));
    if ($objViaje->Buscar($idViaje)) {
        echo "\n".$objViaje;
    }else {
        echo "\nNo existe tal viaje con ese id.\n";
    }
}

function modificarPasajero()
{
    $bandera = false;
    $pasajero = new Pasajero();
    $infoPasajeros = $pasajero->listar();
    foreach ($infoPasajeros as $pasajero) {
        echo "{$pasajero}\n";
        echo "-------\n";
    }
    if (count($infoPasajeros) > 0) {
        while (!$bandera) {
            echo "Ingrese el numero de documento del pasajero al que desea cambiarle los datos:\n";
            $numDocPasajero = trim(fgets(STDIN));
            if ($numDocPasajero != null && $pasajero->Buscar($numDocPasajero)) {
                $pasajeroEncontrado = $pasajero;
                while (true) {
                    echo "Que dato quiere modificar?\n";
                    echo "1- Nombre\n";
                    echo "2- Apellido\n";
                    echo "3- Numero de telefono\n";
                    echo "4- Todos los datos\n";
                    echo "5- Volver al menu principal\n";
                    $eleccion = trim(fgets(STDIN));
                    if ($eleccion == 5) {
                        echo "Regresando al menu principal...\n";
                        $bandera = true;
                        break;
                    } elseif ($eleccion >= 1 && $eleccion <= 4) {
                        switch ($eleccion) {
                            case 1:
                                modificarNombrePasajero($pasajeroEncontrado);
                                break;
                            case 2:
                                modificarApellidoPasajero($pasajeroEncontrado);
                                break;
                            case 3:
                                modificarTelefonoPasajero($pasajeroEncontrado);
                                break;
                            case 4:
                                modificarTodosDatosPasajero($pasajeroEncontrado);
                                break;
                            default:
                                break;
                        }
                    } else {
                        echo "Opcion incorrecta, por favor ingrese una opcion valida\n";
                    }
                }
            } else {
                echo "No se encontro ningun pasajero con ese numero de documento.\n";
            }
        }
    } else {
        echo "\nNo hay pasajeros para modificar.\n";
    }
}

function modificarNombrePasajero($pasajero)
{
    echo "El nombre actual es: " . $pasajero->getNombre() . "\n";
    $nuevoNombre = trim(fgets(STDIN));
    $pasajero->setNombre($nuevoNombre);
    $pasajero->modificar();
    echo "Se cambio correctamente a " . $pasajero->getNombre() . "\n";
    echo "-------\n";
}

function modificarApellidoPasajero($pasajero)
{
    echo "El apellido actual es: " . $pasajero->getApellido() . "\n";
    $nuevoApellido = trim(fgets(STDIN));
    $pasajero->setApellido($nuevoApellido);
    $pasajero->modificar();
    echo "Se cambio correctamente a " . $pasajero->getApellido() . "\n";
    echo "-------\n";
}

function modificarTelefonoPasajero($pasajero)
{
    echo "El telefono actual es: " . $pasajero->getTelefono() . "\n";
    $nuevoTelefono = trim(fgets(STDIN));
    $pasajero->setTelefono($nuevoTelefono);
    $pasajero->modificar();
    echo "Se cambio correctamente a " . $pasajero->getTelefono() . "\n";
    echo "-------\n";
}

function modificarTodosDatosPasajero($pasajero)
{
    modificarNombrePasajero($pasajero);
    modificarApellidoPasajero($pasajero);
    modificarTelefonoPasajero($pasajero);
}

function agregarPasajero()
{
    $bandera = false;
    $viaje = new Viaje();
    $viajes = $viaje->listar();
    foreach ($viajes as $viaje) {
        echo "\nID viaje: {$viaje->getCodigo()} \n";
        echo "Destino viaje: {$viaje->getDestino()} \n";
        echo "-------\n";
    }
    if (count($viajes) > 0) {
        echo "Ingrese el id del viaje.\n";
        $id = trim(fgets(STDIN));
        if ($viaje->Buscar($id)) {
            $cantPasajeros = $viaje->getMaxPasajeros();
            $coleccionPasajeros = $viaje->getPasajerosArray();
            if (count($coleccionPasajeros) < $cantPasajeros) {
                while (!$bandera) {
                    $documento = readline("Numero de documento del pasajero: ");
                    $persona = new Persona();
                    // Verifica que no haya otra persona con el mismo documento. Utilizamos la clase persona porque puede ser un responsable o un pasajero con ese documento
                    $encontrado = $persona->Buscar($documento);
                    while ($encontrado) {
                        $documento = readline("No se puede repetir el DNI Ingrese otro: ");
                        $encontrado = $persona->Buscar($documento);
                    }
                    
                        $nombre = readline("Nombre del pasajero: ");
                        $apellido = readline("Apellido del pasajero: ");
                        $telefono = readline("Telefono del pasajero: ");
                    $asiento = readline("Numero de asiento: ");
                    $ticket = readline("Ingrese el numero de ticket: ");

                    $arrayParam = [
                        'nrodoc' => $documento,
                        'apellido' => $apellido,
                        'nombre' => $nombre,
                        'telefono' => $telefono,
                        'idPasajero' => null,
                        'numAsiento' => $asiento,
                        'numTicket' => $ticket,
                        'objViaje' => $viaje
                    ];
                    $pasajero = new Pasajero();
                    $pasajero->cargar($arrayParam);
                    if ($pasajero->insertar()) {
                        echo "\nEl pasajero fue ingresado con exito\n";
                        $bandera = true;
                    }
               }
            } else {
                echo "Se completo el cupo de la cantidad de pasajeros\n";
            }
        } 
    }else{
        echo "\nNo existe un viaje por lo que no puede cargar un pasajero.\n";
    }
}

function eliminarPasajero()
{
    $pasajero = new pasajero();
    $infopasajero = $pasajero->listar();
    foreach ($infopasajero as $pasajero) {
        echo "\n{$pasajero}\n";
        echo "-------\n";
    }
    if (count($infopasajero) > 0) {
        echo "\nIngrese el DNI del pasajero a eliminar? ";
        $rta = trim(fgets(STDIN));
        if ($rta != null && $pasajero->Buscar($rta)) {
            if ($pasajero->Eliminar()) {
                echo "\nSe elimino con exito.\n";
            }
        } else {
            echo "\nEl pasajero que quiere eliminar no existe o no ingreso un DNI.\n";
        }
    } else {
        echo "\nNo hay ningun pasajero para eliminar.\n";
    }
}

function modificarResponsableViaje($viaje, $opcion)
{
    $responsable = new ResponsableV();
    $responsable->Buscar($viaje->getObjResponsable());
    switch ($opcion) {
        case 1:
            echo "El numero de licencia es: {$responsable->getNumeroLicencia()} \n";
            echo "Ingrese el NUEVO numero de licencia: ";
            $nuevoNumLicencia = trim(fgets(STDIN));
            if ($nuevoNumLicencia != null && is_int($nuevoNumLicencia)) {
                $responsable->setNumeroLicencia($nuevoNumLicencia);
                $responsable->modificar();
                echo "\nSe cambio correctamente a " . $responsable->getNumeroLicencia() . "\n";
            } else {
                echo "\nSOLO NUMEROS.\n";
            }
            break;
        case 2:
            echo "El nombre del empleado: {$responsable->getNombre()} \n";
            echo "Ingrese el NUEVO nombre del empleado: ";
            $nuevoNombre = trim(fgets(STDIN));
            $responsable->setNombre($nuevoNombre);
            $responsable->modificar();
            echo "\nSe cambio correctamente a " . $responsable->getNombre() . "\n";
            break;
        case 3:
            echo "El apellido del empleado: {$responsable->getApellido()} \n";
            echo "Ingrese el NUEVO apellido del empleado: ";
            $nuevoApellido = trim(fgets(STDIN));
            $responsable->setApellido($nuevoApellido);
            $responsable->modificar();
            echo "\nSe cambio correctamente a " . $responsable->getApellido() . "\n";
            break;
        case 4:
            echo "El numero de licencia es: {$responsable->getNumeroLicencia()} \n";
            echo "Ingrese el NUEVO numero de licencia: ";
            $nuevoNumLicencia = trim(fgets(STDIN));
            if ($nuevoNumLicencia != null || is_int($nuevoNumLicencia)) {
                $responsable->setNumeroLicencia($nuevoNumLicencia);
                $responsable->modificar();
                echo "\nSe cambio correctamente a " . $responsable->getNumeroLicencia() . "\n";
            } else {
                echo "\nSOLO NUMEROS.\n";
            }

            echo "\nIngrese el NUEVO nombre del empleado: ";
            $nuevoNombre = trim(fgets(STDIN));
            $responsable->setNombre($nuevoNombre);
            $responsable->modificar();

            echo "\nIngrese el NUEVO apellido del empleado: ";
            $nuevoApellido = trim(fgets(STDIN));
            $responsable->setApellido($nuevoApellido);
            $responsable->modificar();

            echo "Datos del responsable modificados correctamente\n";
            break;
        case 5:
            break;
        default:
            echo "Opcion incorrecta, por favor ingrese una opcion valida\n";
            break;
    }
}

function cargarEmpresa()
{
    $empresa = new Empresa();
        do {
            $nombreEmpresa = readline("Ingrese el nombre de la empresa: ");
            $direccionEmpresa = readline("Ingrese la direccion de la empresa: ");
            if ($nombreEmpresa == "" || $direccionEmpresa == "") {
                echo "--->Los datos no pueden ser vacios.<---\n";
            }
        } while ($nombreEmpresa == "" || $direccionEmpresa == "");

        $empresa->cargar(null, $nombreEmpresa, $direccionEmpresa);
        if ($empresa->insertar()) {
            $empresa->setId(1);
            echo "La empresa fue agregada exitosamente\n";
        } else {
            echo "La empresa no ha podido ser cargada " . $empresa->getmensajeoperacion() . "\n";
        }
}

function modificarEmpresaDatos($empresaAModificar, $eleccion)
{
    switch ($eleccion) {
        case 1:
            echo "El nombre actual de la empresa es " . $empresaAModificar->getNombre() . "\n";
            $nuevoNombre = trim(fgets(STDIN));
            $empresaAModificar->setNombre($nuevoNombre);
            $empresaAModificar->modificar();
            echo "El nombre de la empresa se cambio correctamente\n";
            break;
        case 2:
            echo "La direccion actual de la empresa es " . $empresaAModificar->getDireccion() . "\n";
            $nuevaDir = trim(fgets(STDIN));
            $empresaAModificar->setDireccion($nuevaDir);
            $empresaAModificar->modificar();
            echo "La direccion de la empresa se cambio correctamente\n";
            break;
        case 3:
            echo "El nombre actual de la empresa es " . $empresaAModificar->getNombre() . "\n";
            $nuevoNombre = trim(fgets(STDIN));
            $empresaAModificar->setNombre($nuevoNombre);
            $empresaAModificar->modificar();
            echo "El nombre de la empresa se cambio correctamente\n";

            echo "La direccion actual de la empresa es " . $empresaAModificar->getDireccion() . "\n";
            $nuevaDir = trim(fgets(STDIN));
            $empresaAModificar->setDireccion($nuevaDir);
            $empresaAModificar->modificar();
            echo "La direccion de la empresa se cambio correctamente\n";
            break;
        default:
            echo "Opcion incorrecta, por favor ingrese una opcion valida\n";
            break;
    }
}

function eliminarEmpresa()
{
    $empresa = new Empresa();
    $infoempresa = $empresa->listar();
    foreach ($infoempresa as $empresa) {
        echo "\n{$empresa}\n";
        echo "-------\n";
    }
    echo "Advertencia, se eliminarán todos los viajes de esta empresa \n";
    echo "\nIngrese el codigo del empresa a eliminar? ";
    $rta = trim(fgets(STDIN));
    if ($empresa->Buscar($rta)) {
        $viaje = new Viaje();
        $arreglosViajeEmpresa = $viaje->listar("idempresa =". $rta);
        foreach($arreglosViajeEmpresa as $viaje){
            
            $idViaje = $viaje->getCodigo();
            $pasajero= new Pasajero();
                $arrayPasajerosViaje = $pasajero->listar("idviaje =". $idViaje);
                foreach($arrayPasajerosViaje as $pasajero){
                    $pasajero->eliminar();
                }
                $responsable = new ResponsableV ();
            $numEmpleado = $viaje->getObjResponsable()->getNumeroEmpleado();
            echo $numEmpleado;
            $viaje->Eliminar();
            $responsable->Buscar($numEmpleado);
            $responsable->eliminar();
    
        }
        if ($empresa->Eliminar()) {
            echo "\nSe elimino con exito.\n";
        }
    } else {
        echo "\nLa empresa que quiere eliminar no existe.\n";
    }
}
