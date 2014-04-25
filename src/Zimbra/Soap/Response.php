<?php
/**
 * This file is part of the Zimbra API in PHP library.
 *
 * © Nguyen Van Nguyen <nguyennv1981@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zimbra\Soap;

use Guzzle\Http\Message\Response as HttpResponse;
use Zimbra\Common\SimpleXML;

/**
 * Response class in Zimbra API PHP.
 * 
 * @package   Zimbra
 * @category  Soap
 * @author    Nguyen Van Nguyen - nguyennv1981@gmail.com
 * @copyright Copyright © 2014 by Nguyen Van Nguyen.
 */
class Response
{
    /**
     * Soap response object
     * @var object
     */
    private $_response;

    /**
     * Response constructor
     *
     * @param  HttpResponse $httpResponse
     * @return self
     */
    public function __construct(HttpResponse $httpResponse = null)
    {
		$this->_response = $this->processXml($httpResponse->getBody(true));
    }

    /**
     * Returns a property value.
     * @param string $name the property name
     * @return mixed the property value
     * @throws Exception if the property not defined
     */
    public function __get($name)
    {
        if(isset($this->_response->$name))
        {
            return $this->_response->$name;
        }
        else
        {
            throw new \RuntimeException('Property ' . $name . ' is not defined.');
        }
    }

    /**
     * Checks if a property value is null.
     * @param string $name the property name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->_response->$name);
    }

    /**
     * Process soap response xml.
     *
     * @param  string $xml Soap response message in xml format.
     * @throws RuntimeException
     * @return mix
     */
    protected function processXml($xml)
    {
        if(empty($xml))
        {
            throw new \UnexpectedValueException('Response string is empty.');
        }
        $xml = new SimpleXML($xml);
        $fault = $xml->children('soap', true)->Body->Fault;
        if ($fault)
        {
            throw new \RuntimeException($fault->children('soap', true)->Reason->Text);
        }

        return $xml->children('soap', true)->Body->children()->toObject();
    }
}