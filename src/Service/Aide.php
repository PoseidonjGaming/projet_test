<?php
namespace App\Service;

use Knp\Component\Pager\PaginatorInterface;

class Aide{
    public function pager($items, PaginatorInterface $paginator,$field, $dir, $request){
        $pagerItems=$paginator->paginate(
            $items,$request->query->getInt('page',1),
            15,
            [
                'defaultSortFieldName'=>$field,
                "defaultSortDirection"=>$dir
            ]
        );
        return $pagerItems;
    }
}