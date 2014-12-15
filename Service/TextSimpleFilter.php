<?php

namespace Swaroge\SimpleTextFilterBundle\Service;
use \Symfony\Bundle\FrameworkBundle\Client;

class TextSimpleFilter 
{
    
    public function __construct()
    {

    }
    
    /**
     *@data - array
     * 
    */
    public function queryFilter($query,$data,$entityName = null){

        if(is_object($data)){
          $data = $data->jsonSerializeFilter();
        }
        $alias = current($query->getDQLPart('from'))->getAlias();
        
        if($data){
                foreach($data as $k=>$v){
                   
                    if(!$v) {
                        continue;
                    }

                    if(is_object($v) && get_class($v) != $entityName){

                        /*check if this entity is not self*/
                        if(get_class($v)== $entityName){

                        }

                        if(count($v) == 0) continue;
                        
                        if(method_exists($v, 'getName')){
                            $vstr = $v->getName();
                            $field = 'name';
                        }
                        if(method_exists($v, 'getTitle')){
                            $vstr = $v->getTitle();
                            $field = 'title';
                        }
                        if(method_exists($v, 'getFilterKeys')){

                            $filterKey = $v->getFilterKeys();
                            $method = array_values($filterKey)[0];
                            $vstr = $v->{$method}();
                            $field = array_keys($filterKey)[0];
                        }
                        $class_alias = explode("\\",get_class($v));
                        $query->leftJoin($alias.".".strtolower($class_alias[count($class_alias)-1]) , $k);
                        $query->andWhere($query->expr()->like($k.'.'.$field, ":text_".$k."_".$field))->setParameter("text_".$k."_".$field,'%' . trim($vstr) . '%');

                    }else{
                        if(is_object($v)){

                            if(method_exists($v, 'getFilterKeys')){

                                $filterKey = $v->getFilterKeys();
                                $v = $v->{$filterKey[$k]}();
                            }
                        };
                        $query->andWhere($query->expr()->like($alias.'.'.$k,":text_".$alias."_".$k))->setParameter('text_'.$alias."_".$k,'%'.$v.'%');
                    }
                    
                }
            
        }
        return $query;
    }
    
    
}