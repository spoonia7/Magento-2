<?php
use \Magento\Framework\Component\ComponentRegistrar;
ComponentRegistrar::register(
    ComponentRegistrar::THEME,
    'frontend/Local/child-theme',
    __DIR__
);