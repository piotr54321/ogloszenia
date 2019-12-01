<?php

function restructureFilesArray($files)
{
	$output = [];
	foreach ($files as $attrName => $valuesArray) {
		foreach ($valuesArray as $key => $value) {
			$output[$key][$attrName] = $value;
		}
	}
	return $output;
}
