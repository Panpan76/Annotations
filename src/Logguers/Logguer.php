<?php

namespace Logguers;

class Logguer implements \Annotations\Interfaces\Logguable{
  ###############
  ## Attributs ##
  ###############
  const DOSSIER_LOG       = 'logs/'; // Emplacement des logs
  const REGEX_LOG_FORMAT  = "{date}\t{niveau}/{message}\n"; // Format du message

  private $file;

  ##############
  ## Méthodes ##
  ##############

  /**
   * Constructeur de la classe Logguer
   * Soumis à un pattern singleton
   *
   * @param string $fichier Fichier dans lequel les logs seront écrit
   */
  public function __construct(){
    if(!is_dir(self::DOSSIER_LOG)){ // Si le dossier de log n'existe pas, on le crée
      mkdir(self::DOSSIER_LOG, 0777, true);
    }
  }


  public function setLogFile(?string $file):void{
    $this->file = self::DOSSIER_LOG.$file.'_'.date('Y_m_d').'.log';
  }

  public function log(string $message, string $type = 'I'):void{
    try{
      // Utilisation du gestionnaire de fichier
      $fichier = fopen($this->file, 'a');

      $temps = microtime(true); // On active les microsecondes
      $micro = sprintf("%06d", ($temps - floor($temps)) * 1000000);
      $date = new \DateTime(date('Y-m-d H:i:s.'.$micro, $temps)); // On récupère la date avec les microsecondes
      $date = $date->setTimeZone(new \DateTimeZone('Europe/Paris')); // On définit le timezone

      $str = self::REGEX_LOG_FORMAT; // On utilise le format défini en constante de classe

      // On remplace les infos
      $str = str_replace('{date}', $date->format('Y/m/d H:i:s.u'), $str);
      $str = str_replace('{niveau}', $type, $str);
      $str = str_replace('{message}', $message, $str);

      fwrite($fichier, $str); // On écrit
      fclose($fichier); // On ferme le fichier
    }catch(FichierException $e){
      echo $e;
    }

  }
}
