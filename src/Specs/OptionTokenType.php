<?php

namespace CommandParser\Specs;

/**
 * Commandline Option Token Type.
 *
 * @api
 * @since 0.1.0
 * @version 1.0.0
 * @package command-parser
 * @author Ali M. Kamel <ali.kamel.dev@gmail.com>
 */
enum OptionTokenType: string {

    case Standard = '-';

    case Extended = '--';
}
