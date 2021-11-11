<!DOCTYPE html>
<!--
    Autor: Isabel Martínez Guerra
    Fecha: 08/11/2021
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>IMG - DWES 4-8 PDO</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <main>
            <?php
            /*
             * Fecha de creación: 10/10/2021
             * Fecha de última modificación: 10/10/2021
             * @version 1.0
             * @author Sasha
             * 
             * Toma datos de la tabla Departamento y los guarda en departamento.xml
             * (copia de seguridad/exportar).
             */

            // Constantes para la conexión con la base de datos.
            require_once '../config/configDBPDO.php';

            /**
             * Recogida de los datos de la tabla Departamento.
             */
            try {
                $sSentencia = 'SELECT * FROM Departamento';
                
                // Conexión con la base de datos.
                $oDB = new PDO(HOST, USER, PASSWORD);

                // Mostrado de las excepciones.
                $oDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Consulta preparada.
                $oConsulta = $oDB->prepare($sSentencia);

                // Comienzo de la transacción.
                $oDB->beginTransaction();
                
                // Ejecución del select.
                $oConsulta->execute();
                
                /*
                 * Creación del XML que formatee la salida con indentación y espacios.
                 */
                $oDoc = new DOMDocument();
                $oDoc -> formatOutput = true;
                
                $oElemDepartamentos = $oDoc->createElement("departamentos");
                $nodoDepartamentos = $oDoc->appendChild($oElemDepartamentos);
                
                /*
                 * Recogida de información y escritura del archivo.
                 */
                $oDepartamento = $oConsulta->fetchObject();
                
                while($oDepartamento){
                    // Creación del elemento departamento.
                    $oElemDepartamento = $oDoc->createElement("departamento");
                    $nodoDepartamentos->appendChild($oElemDepartamento);
                    
                    // Creación y añadido de la información sobre el departamento.
                    $oElemCodigo = $oDoc->createElement('codDepartamento', $oDepartamento->codDepartamento);
                    $oElemDepartamento->appendChild($oElemCodigo);
                    
                    $oElemCodigo = $oDoc->createElement('descDepartamento', $oDepartamento->descDepartamento);
                    $oElemDepartamento->appendChild($oElemCodigo);
                    
                    $oElemCodigo = $oDoc->createElement('fechaBaja', $oDepartamento->fechaBaja);
                    $oElemDepartamento->appendChild($oElemCodigo);
                    
                    $oElemCodigo = $oDoc->createElement('volumenNegocio', $oDepartamento->volumenNegocio);
                    $oElemDepartamento->appendChild($oElemCodigo);
                    
                    $oDepartamento = $oConsulta->fetchObject();
                }
                
                // Guardado del archivo.
                echo $oDoc->save('../tmp/prueba.xml').' bytes escritos';
                

                /*
                 * Si todo ha salido bien, commitea cambios.
                 */
                $oDB->commit();
                
            } catch (PDOException $exception) {
                /*
                 * Si se han dado errores, hace rollback.
                 */
                $oDB->rollBack();
                /*
                 * Mostrado del código de error y su mensaje.
                 */
                echo '<div>Se han encontrado errores:</div><ul>';
                echo '<li>' . $exception->getCode() . ' : ' . $exception->getMessage() . '</li>';
                echo '</ul>';
            } finally {
                unset($oDB);
            }
            
            ?>
        </main>
    </body>
</html>
