<?php
/* website API
 * ~ website api
 * authored by 9r3i
 * https://github.com/9r3i
 * started at november 5th 2019
 * @require:
 *   - apix
 */
#[AllowDynamicProperties]
class webAPI extends apix{
  const version='1.0.0';
  public function __construct(){
    parent::__construct([
      'methods'=>[
        '',
      ],
      'prefixKey'=>'eday-website-',
    ]);
    return $this->start();
  }
}


