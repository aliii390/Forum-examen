<?php

namespace App\Service;

use Exception;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    public function __construct(
        private string $targetDirectory,
        private SluggerInterface $slugger,
    ) {

    }
    
    // function qui me permet d'upload une photo 
    // les 4 parametres : la photo a upload, l'entity lier , le nom du champs dans l'entity, le nom du dossier ou on stock les photo
    public function upload(UploadedFile $file, object $entity, string $fieldName, string $folder): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
         // je  "sluggifie" pour éviter les caractères spéciaux ou espaces
        $safeFilename = $this->slugger->slug($originalFilename);
         // je génère un nom de fichier unique avec une extension conservée
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
    

        try {
          
            // je déplace le fichier dans le dossier cible
            $file->move($this->getTargetDirectory() . '/' . $folder, $fileName);
        } catch (FileException $e) {
            throw new Exception('un problème est survenu lors du téléchargement');
        }


        // je supprime l'ancien fichier lié à cette entité si existant
        $this->removeOldFile($entity, $fieldName, $folder);

        return $fileName;
    }

       // permet de récupérer le chemin de base de stockage
    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }

    // function qui me permet de supprimer la photo de profil 
    public function removeOldFile(object $entity, string $fieldName, string $folder): void
    {
        $getter = 'get' . ucfirst($fieldName);
        $oldFile = $entity->$getter();

        if ($oldFile) {
            $oldFilePath = $this->getTargetDirectory() . '/' . $folder . '/' . $oldFile;
            // si une photo existe et ben on la supprime grâce a unlink
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
        }
    }
}