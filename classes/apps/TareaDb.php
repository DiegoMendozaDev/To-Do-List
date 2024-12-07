<?php
    class TareaDb extends Conex{

        private $idTarea;
        private $nombre;
        private $descripcion;
        private $fechaFin;
        private $idGrupo;
        private $idUsuario;

        public function __construct(){
            parent::__construct();    
        }

        public function getNombre(){
            return $this->nombre;
        }
        public function getDescripcion(){
            return $this->descripcion;
        }
        public function getFechaFin(){
            return $this->fechaFin;
        }
        public function getIdTarea(){
            return $this->idTarea;
        }

        public function anadirTarea($nombre,$descripcion,$fechaFin,$idGrupo,$idUsuario){
            $query = 'INSERT INTO tarea(nombre,descripcion,fechaFin,idGrupo,idUsuario) VALUES (:nombre,:descripcion,:fechaFin,:idGrupo,:idUsuario)';
            try{
                $insertar = $this->conexBd->prepare($query);
                $insertar->execute([':nombre'=>$nombre,':descripcion'=>$descripcion,':fechaFin'=>$fechaFin,':idGrupo'=>$idGrupo,':idUsuario'=>$idUsuario]);
            }catch(PDOException $e){
                echo 'Error al insertar tarea en la base de datos: '.$e->getMessage(). ', en la linea: '.$e->getLine().', en el archivo: '.$e->getFile();
            }
        }
        public function eliminarTarea($idTarea){
            $query = 'DELETE FROM tarea WHERE idTarea=:idTarea';
            try{
                $eliminar = $this->conexBd->prepare($query);
                $eliminar->execute([':idTarea'=>$idTarea]);
            }catch(PDOException $e){
                echo 'Error al eliminar tarea en la base de datos: '.$e->getMessage(). ', en la linea: '.$e->getLine().', en el archivo: '.$e->getFile();
            }
        }
        public function editarTarea($idTarea,$nombre,$descripcion,$fechaFin){
            $query = 'UPDATE tarea SET nombre=:nombre,descripcion=:descripcion,fechaFin=:fechaFin WHERE idTarea=:idTarea';
            try{
                $actualizar = $this->conexBd->prepare($query);
                $actualizar->execute([':idTarea'=>$idTarea,':nombre'=>$nombre,':descripcion'=>$descripcion,':fechaFin'=>$fechaFin,]);
            }catch(PDOException $e){
                echo 'Error al actualizar el nombre del grupo: '.$e->getMessage(). ', en la linea: '.$e->getLine().', en el archivo: '.$e->getFile();
            }
        }
        public function seleccionarTareasUsuarios($idUsuario){
            $query = 'SELECT * FROM tarea WHERE idUsuario=:idUsuario AND idGrupo IS NULL';
            try{
                $selecionar = $this->conexBd->prepare($query);
                $selecionar->execute([':idUsuario'=>$idUsuario]);
                $selecionar->setFetchMode(PDO::FETCH_CLASS,'TareaDb');
                return $selecionar;
            }catch(PDOException $e){
                echo 'Error al seleccionar el grupo: '.$e->getMessage(). ', en la linea: '.$e->getLine().', en el archivo: '.$e->getFile();
            }
        }
        public function seleccionarTarea($idTarea){
            $query = 'SELECT * FROM tarea WHERE idTarea=:idTarea AND idGrupo IS NULL';
            try{
                $selecionar = $this->conexBd->prepare($query);
                $selecionar->execute([':idTarea'=>$idTarea]);
                $selecionar->setFetchMode(PDO::FETCH_CLASS,'TareaDb');
                $tarea = $selecionar ->fetch();
                return $tarea;
            }catch(PDOException $e){
                echo 'Error al seleccionar el grupo: '.$e->getMessage(). ', en la linea: '.$e->getLine().', en el archivo: '.$e->getFile();
            }
        }
        
    }
?>