<?php
namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * Setting.
 *
 * @ORM\Entity
 * @ORM\Table(name="Setting")
 * @property int $userId
 * @property int $payDay
 */
class Setting implements InputFilterAwareInterface
{

    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(name="userId", type="integer", options={"unsigned"=true});
     */
    protected $userId;

    /**
     * @ORM\Column(name="payDay", type="integer", options={"unsigned"=true})
     */
    protected $payDay = 0;

    /**
     * Magic getter to expose protected properties.
     *
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        return $this->$property;
    }

    /**
     * Magic setter to save protected properties.
     *
     * @param string $property
     * @param mixed $value
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * Populate from an array.
     *
     * @param array $data
     * @return Setting
     */
    public function exchangeArray($data = array())
    {
        $this->userId = isset($data['userId']) ? $data['userId'] : null;
        $this->payDay = isset($data['payDay']) ? $data['payDay'] : null;
        return $this;
    }

    /**
     * Set input filter
     *
     * @param  InputFilterInterface $inputFilter
     * @return InputFilterAwareInterface
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    /**
     * @return InputFilter
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $this->inputFilter = new InputFilter();
            $this->inputFilter->add(array(
                'filters'  => array(array('name' => 'Zend\Filter\ToInt')),
                'name'     => 'payDay',
                'required' => true,
            ));
        }
        return $this->inputFilter;
    }
}