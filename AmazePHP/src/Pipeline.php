<?php
namespace AmazePHP;


use InvalidArgumentException as InvalidArgumentException ;
use Closure as Closure;
use MiddlewareInterface as MiddlewareInterface;

class Pipeline
{
    private $pipes;

    public function __construct(array $pipes = [])
    {
        $this->pipes = $pipes;
    }

    /**
     * Add layer(s) or Pipeline
     * @param  mixed $pipes
     * @return Pipeline
     */
    public function through($pipes)
    {
        if ($pipes instanceof Pipeline) {
            $pipes = $pipes->toArray();
        }

        if ($pipes instanceof MiddlewareInterface) {
            $pipes = [$pipes];
        }

        if (!\is_array($pipes)) {
            throw new InvalidArgumentException(\get_class($pipes) . " is not a valid onion layer.");
        }

        return new static(array_merge($this->pipes, $pipes));
    }

    /**
     * Run middleware around core function and pass an
     * object through it
     * @param  mixed  $object
     * @param  Closure $core
     * @return mixed
     */
    public function then($object, Closure $core)
    {
        $coreFunction = $this->createCoreFunction($core);

        // Since we will be "currying" the functions starting with the first
        // in the array, the first function will be "closer" to the core.
        // This also means it will be run last. However, if the reverse the
        // order of the array, the first in the list will be the outer pipes.
        $pipes = array_reverse($this->pipes);

        // We create the onion by starting initially with the core and then
        // gradually wrap it in pipes. Each layer will have the next layer "curried"
        // into it and will have the current state (the object) passed to it.
        $completeOnion = array_reduce($pipes, function ($nextLayer, $layer) {
            return $this->createLayer($nextLayer, $layer);
        }, $coreFunction);

        // We now have the complete onion and can start passing the object
        // down through the pipes.
        return $completeOnion($object);
    }

    /**
     * Get the pipes of this onion, can be used to merge with another onion
     * @return array
     */
    public function toArray()
    {
        return $this->pipes;
    }

    /**
     * The inner function of the onion.
     * This function will be wrapped on pipes
     * @param  Closure $core the core function
     * @return Closure
     */
    private function createCoreFunction(Closure $core)
    {
        return function ($object) use ($core) {
            return $core($object);
        };
    }



    /**
     * Parse full pipe string to get name and parameters.
     *
     * @param  string  $pipe
     * @return array
     */
    protected function parsePipeString($pipe)
    {
        [$name, $parameters] = array_pad(explode(':', $pipe, 2), 2, []);

        if (\is_string($parameters)) {
            $parameters = explode(',', $parameters);
        }

        return [$name, $parameters];
    }

    /**
     * Get an onion layer function.
     * This function will get the object from a previous layer and pass it inwards
     * @param  MiddlewareInterface $nextLayer
     * @param  MiddlewareInterface $layer
     * @return Closure
     */
    private function createLayer($nextLayer, $layer)
    {
        if (!\is_object($layer)) {
            [$name, $parameters] = $this->parsePipeString($layer);
            $layer =new $name;
        }
        $parameters=$parameters ?? [];
        return function ($object) use ($nextLayer, $layer, $parameters) {
            $response= $layer->process($object, $nextLayer, $parameters);
            //  if (!$response instanceof Response){
            //      throw new \Exception("The middleware must return a response object", 1);
            //  }
             return $response;
        };
    }



    private function createLayer_yuan($nextLayer, $layer)
    {
        return function($object) use($nextLayer, $layer){
            return $layer->peel($object, $nextLayer);
        };
    }
}