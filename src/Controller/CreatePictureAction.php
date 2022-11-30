<?php

namespace App\Controller;

use App\Entity\Picture;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
final class CreatePictureAction extends AbstractController
{
/**
 * > It takes a file from the request, creates a new Picture object, and sets the file on the Picture
 * object.
 * 
 * The first line of the function is a type hint. It tells PHP that the function expects a Request
 * object. If you don't pass a Request object, PHP will throw an error.
 * 
 * The second line gets the file from the request. The `files` property is an array of files. The `get`
 * method gets the file from the array. If the file doesn't exist, the function throws a
 * BadRequestHttpException.
 * 
 * The third line creates a new Picture object.
 * 
 * The fourth line sets the file on the Picture object.
 * 
 * The fifth line returns the Picture object.
 * 
 * @param Request request The request object
 * 
 * @return Picture A new instance of the Picture class.
 */
    public function __invoke(Request $request): Picture
    {
        $uploadedFile = $request->files->get('file');
        if (!$uploadedFile) {
            throw new BadRequestHttpException('"file" is required');
        }

        $picture = new Picture();
        $picture->setFile($uploadedFile);

        return $picture;
    }
}