<?php
namespace Library\Format;

class Path implements IFormat
{
    /**
     * {@inheritDoc}
     * @see \Library\Format\IFormat::format()
     */
    public static function format(string $input): string
    {
        $tmpArr = explode('/', $input);
        $data = [];
        foreach ($tmpArr as $v) {
            if ($v=='.') {
                continue;
            } else if ($v=='..') {
                array_pop($data);
            } else {
                array_push($data, $v);
            }
        }
        return implode(DIRECTORY_SEPARATOR, $data);
    }
}

