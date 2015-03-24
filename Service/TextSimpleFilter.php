<?php

namespace Swaroge\SimpleTextFilterBundle\Service;
use \Symfony\Bundle\FrameworkBundle\Client;

class TextSimpleFilter 
{

    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }


    /**
     * Clear the filter session
     * @param $form
     */
    public function clearFilter($form){
        $data = $this->container->get('request')->getSession()->set($form->getName(),null);
    }

    protected function getFilter($form){
        $data = $this->container->get('request')->getSession()->get($form->getName());
        return ($data) ? $data : null;
    }


    protected function setFilter($form){

        $this->container->get('request')->getSession()->set($form->getName(),$this->container->get('request')->get($form->getName()));
    }

    public function processFilter($filterForm)
    {
        //1 check session
        if($this->container->get('request')->get($filterForm->getName())){
            // handle request to form
            $filterForm->handleRequest($this->container->get('request'));
            // write form data to session
            $this->setFilter($filterForm);
        }else{

            //2 if session exist
            $filterData = $this->getFilter($filterForm);
            $filterForm->submit($filterData);
        }

        //3 return form
        return $filterForm;
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
//                    var_dump($v);
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
                    $query->andWhere($query->expr()->like($k.'.'.$field, ":text_".$k."_".$field))->setParameter("text_".$k."_".$field,"%" . trim($vstr) . '%');
                }else{
                    if(is_object($v)){


                        if(method_exists($v, 'getFilterKeys')){

                            $filterKey = $v->getFilterKeys();
                            $v = $v->{$filterKey[$k]}();
                        }
                    };
                    $query->andWhere($query->expr()->like($alias.'.'.$k,":text_".$alias."_".$k))->setParameter('text_'.$alias."_".$k,"%" . trim($v).'%');
                }

            }

        }

        return $query;
    }
    
    
}