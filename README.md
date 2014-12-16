SimpleTextFilterBundle
======================
Its simple filter for Symfony 2 Entity.

### Installation

> composer
* "require" section 
```json
    "require": {
        "swaroge/simple-text-filter-bundle" : "dev-master"
    }
```
* "repositories" section
```json
    "repositories" : [{
        "type" : "vcs",
        "url" : "https://github.com/swaroge/SimpleTextFilterBundle.git"
    }],
```
``` bash
$ composer update
```
* add service in services.yml
``` yml
$ composer update
```
### Usage
1. Create FormType for filter
2. Create jsonSerializeFilter metod in Entity
3. Changes in controller
4. Twig example

#### Create FormType 
  create new form type for filter form:

    <?php
    namespace My\TestBundle\Form;
    /* ... */
    class TestFormType 
    {
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
        ;
    }
    
  
    }
    /* ... */
    /**
     * @return string
     */
    public function getName()
    {
        return 'my_testbundle_filter';
    }
}
    
#### Create jsonSerializeFilter metod in Entity
dont forgott

    <?php
    class EntityName implements \JsonSerializable
create public method in class

     <?php
    public function jsonSerializeFilter() {
        /* fields used in form */
        return [
            'name'=>$this->getAddress(),
        ];
    }
    
### Changes in controller    
        <?php
        use Swaroge\SimpleTextFilterBundle\Service\TextSimpleFilter;
        /*...*/
        public function indexAction(){
            $textSimpleFilter = new TextSimpleFilter();
            $form_filter = $this->get('form.factory')->create(new TestFilterType());
            $form_filter->handleRequest($this->get('request'));
        }
        
         $query = $textSimpleFilter->queryFilter($query,$form_filter->getData(),get_class(new Product()));