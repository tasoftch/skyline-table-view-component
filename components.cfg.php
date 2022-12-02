<?php

use Skyline\Component\Config\AbstractComponent;
use Skyline\Component\Config\CSSComponent;
use Skyline\Component\Config\JavaScriptComponent;

return [
	"TableView" => [
    	// Use the JavaScriptComponent to load the scripts in html head tag, so before any content gets loaded.
		"js" => new JavaScriptComponent(
				...AbstractComponent::makeLocalFileComponentArguments(
				"/Public/Skyline/table-view-component.min.js",
				__DIR__ . "/Components/js/skyline.min.js"
			)
		),
        "css" => new CSSComponent(
        	// CSS components are always loaded before the body contents.
			...AbstractComponent::makeLocalFileComponentArguments(
				"/Public/Skyline/table-view-component.min.css",
				__DIR__ . "/Components/css/skyline.min.css",
				'sha384',
				NULL,
				'all'
			)
		)
    ]
];