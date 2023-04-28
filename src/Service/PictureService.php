<?php

declare(strict_types=1);

namespace App\Service;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class PictureService
{
    public function __construct(private readonly ParameterBagInterface $params)
    {
    }

    public function add(UploadedFile $picture, ?string $folder = '', ?int $width = 250, ?int $height = 250): string
    {
        // On donne un nouveau nom à l'image
        $fichier = md5(uniqid((string)random_int(0, mt_getrandmax()), true)) . '.webp';//md5(uniqid((string)rand(), true)) . '.webp';

        // On récupère les infos de l'image
        $picture_infos = getimagesize((string)$picture);

        if($picture_infos === false){
            throw new Exception("Format d'image incorrect");
        }

        /** @var \GdImage $picture_source */
        $picture_source = $this->imagecreatefromfiletype($picture, $picture_infos);

        // On recadre l'image
        // On récupère les dimensions
        $imageWidth = $picture_infos[0];
        $imageHeight = $picture_infos[1];

        $squareSize = 0;
        $src_x = 0;
        $src_y = 0;

        if ($imageWidth < $imageHeight){//-1
            // portrait
            $squareSize = $imageWidth;
            $src_x = 0;
            $src_y = ($imageHeight - $squareSize) / 2;
        }

        if ($imageWidth == $imageHeight){//0
            // carré
            $squareSize = $imageWidth;
            $src_x = 0;
            $src_y = 0;
        }

        if ($imageWidth > $imageHeight){//1
            // paysage
            $squareSize = $imageHeight;
            $src_x = ($imageWidth - $squareSize) / 2;
            $src_y = 0;
        }

        // On crée une nouvelle image "vierge"
        /** @var \GdImage $resized_picture */
        $resized_picture = imagecreatetruecolor((int)$width, (int)$height);

        imagecopyresampled($resized_picture, $picture_source, 0, 0, $src_x, $src_y, (int)$width, (int)$height, $squareSize, $squareSize);

        /** @var string $dir */
        $dir = $this->params->get('images_directory');
        /** @var string $path */
        $path = $dir. $folder;

        // On crée le dossier de destination s'il n'existe pas
        if(!file_exists($path . '/mini/')){
            mkdir($path . '/mini/', 0755, true);
        }

        // On stocke l'image recadrée
        imagewebp($resized_picture, $path . '/mini/' . $width . 'x' . $height . '-' . $fichier);

        $picture->move($path . '/', $fichier);

        return $fichier;
    }

    /** @param array<string> $picture_infos */
    public function imagecreatefromfiletype(UploadedFile $picture, array $picture_infos): mixed
    {
        ///** @var array<string> $picture_infos */
        if ($picture_infos['mime'] == 'image/png'){
            $picture_source = imagecreatefrompng((string)$picture);
        } elseif ($picture_infos['mime'] == 'image/jpeg'){
            $picture_source = imagecreatefromjpeg((string)$picture);
        } elseif ($picture_infos['mime'] == 'image/webp'){
            $picture_source = imagecreatefromwebp((string)$picture);
        } else {
            throw new Exception("Format d'image incorrect");
        }

        return $picture_source;
    }

    public function delete(string $fichier, ?string $folder = '', ?int $width = 250, ?int $height = 250): bool
    {
        if($fichier !== 'default.webp'){
            $success = false;

            /** @var string $dir */
            $dir = $this->params->get('images_directory');
            /** @var string $path */
            $path = $dir. $folder;

            $mini = $path . '/mini/' . $width . 'x' . $height . '-' . $fichier;

            if(file_exists($mini)){
                unlink($mini);
                $success = true;
            }

            $original = $path . '/' . $fichier;

            if(file_exists($original)){
                unlink($original);
                $success = true;
            }

            return $success;
        }

        return false;
    }
}