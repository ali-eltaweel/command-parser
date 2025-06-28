<?php

namespace CommandParser;

/**
 * Parsed Commandline Operand.
 * 
 * @api
 * @final
 * @since 0.1.0
 * @version 1.0.0
 * @package command-parser
 * @author Ali M. Kamel <ali.kamel.dev@gmail.com>
 */
final class Operand {

    /**
     * Creates a new Operand.
     *
     * @api
     * @final
     * @since 1.0.0
     * @version 1.0.0
     *
     * @param mixed       $value The value of the operand.
     * @param int         $index The index of the operand in the command line.
     * @param string|null $name  The name of the operand, if any.
     */
    public final function __construct(

        public readonly mixed   $value,
        public readonly int     $index,
        public readonly ?string $name = null
    ) {}
}
