<?php

namespace Service;

/**
 * \View
 */
class View
{
    const CACHE_PATH = BASE_PATH . '/App/View/Cache';
    const VIEW_PATH = [
        BASE_PATH . '/App/View'
    ];
    
    public $view;
    public $viewName;
    public $data;

    /**
        * 设置对应的view
        *
        * @param $viewName 文件名称
        *
        * @return 
     */
    public function make($viewName = null)
    {
        if ( ! $viewName ) {
            throw new InvalidArgumentException("视图名称不能为空！");
        } else {
            $this->viewName = $viewName;
            $file = new \Xiaoler\Blade\Filesystem;
            $compiler = new \Xiaoler\Blade\Compilers\BladeCompiler($file, self::CACHE_PATH);

            // you can add a custom directive if you want
            $compiler->directive('datetime', function($timestamp) {
                return preg_replace('/(\(\d+\))/', '<?php echo date("Y-m-d H:i:s", $1); ?>', $timestamp);
            });

            $resolver = new \Xiaoler\Blade\Engines\EngineResolver;
            $resolver->register('blade', function () use ($compiler) {
                return new \Xiaoler\Blade\Engines\CompilerEngine($compiler);
            });

            // get an instance of factory
            $factory = new \Xiaoler\Blade\Factory($resolver, new \Xiaoler\Blade\FileViewFinder($file, self::VIEW_PATH));

            // if your view file extension is not php or blade.php, use this to add it
            $factory->addExtension('tpl', 'blade');
            $this->view = $factory;
            // render the template file and echo it
            // echo $factory->make('home', ['a' => 1, 'b' => 2])->render();
            return $this;
        }

    }

    /**
        * 设置参数
        *
        * @param $key 参数名称
        * @param $value 参数值
        *
        * @return 
     */
    public function with($key, $value = null)
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function __call($method, $parameters)
    {
        if (starts_with($method, 'with'))
        {
            return $this->with(snake_case(substr($method, 4)), $parameters[0]);
        }

        throw new BadMethodCallException("方法 [$method] 不存在！.");
    }
}

