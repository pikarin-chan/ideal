<?php

namespace imc\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class imcUserBundle extends Bundle
{
    public function getParent() {

        return  'FOSUserBundle';
    }


}
