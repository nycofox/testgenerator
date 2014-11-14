<?php
// Autload and initiate faker classes
require_once('vendor/fzaninotto/faker/src/autoload.php');
$faker = Faker\Factory::create();

// Load template
$xt = simplexml_load_file('template.xml');

// Start a new XML file
$xml = new XMLWriter();
$xml->openUri('input.xml');
$xml->startDocument('1.0', 'UTF-8');
$xml->setIndent(true);
generateElement($xt);
$xml->endDocument();
$xml->flush();

function getValue($input)
{
    global $faker;
    if (substr($input, 0, 1) === '$') {
        $fnc = str_replace('$', '', $input);
        return $faker->$fnc;
    }
	
	return $input;
}

function generateElement($current)
{
    global $xml;

    // Include the requested number of times
    $currentcount = $current->attributes()->inc ? $current->attributes()->inc : 1;

    for ($x = 1; $x <= $currentcount; $x++) {
        $xml->startElement($current->getName());
        foreach ($current->attributes() as $attribute => $value) {
            if (!($attribute === 'inc')) {
                $xml->startAttribute($attribute);
                $xml->text(getValue($value));
                $xml->endAttribute();
            }
        }

        if ($current->count() > 0) {
            foreach ($current->children() as $child) {
                generateElement($child);
            }
        } else {
            $xml->text(getValue($current));
        }
        $xml->endElement();
    }
}