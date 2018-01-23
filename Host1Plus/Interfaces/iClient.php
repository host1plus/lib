<?php

namespace Host1Plus\Interfaces;

interface iClient
{
    public function __construct(iTransport $transport);
}