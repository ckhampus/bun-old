<?php

require_once(__DIR__.'/../../src/Bun/Cache.php');

class RouteTest extends PHPUnit_Extensions_OutputTestCase {
    public function testRouteWithLifetime() {
        $route = new Route('GET', '/path', function(){}, 30);
        
        $this->assertEquals('GET', $route->method);
        $this->assertEquals('/path', $route->path);
        $this->assertTrue(is_callable($route->callback));
        $this->assertEquals(30, $route->lifetime);
    }
    
    public function testRouteWithoutLifetime() {
        $route = new Route('POST', '/hello', function(){});
        
        $this->assertEquals('POST', $route->method);
        $this->assertEquals('/hello', $route->path);
        $this->assertTrue(is_callable($route->callback));
        $this->assertEquals(0, $route->lifetime);
    }
}