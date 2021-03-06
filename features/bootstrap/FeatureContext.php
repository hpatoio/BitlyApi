<?php

use Behat\Behat\Context\BehatContext;
use Behat\Gherkin\Node\TableNode;

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{

    private $request_parameters;
    private $context_parameters;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $this->context_parameters = $parameters;
        $this->request_parameters = array();
        $this->client = new Hpatoio\Bitly\Client($parameters['access_token']);
    }

    /**
     * @Given /^the following parameters:$/
     */
    public function pushParams(TableNode $parametersTable)
    {
        foreach ($parametersTable->getHash() as $parameterHash) {
            if ($parameterHash['casting']) {
                if ($parameterHash['casting'] == 'Array')
                    $parameterHash['value'] = explode(",",$parameterHash['value']);
                else
                    settype($parameterHash['value'], $parameterHash['casting']);
            }
            $this->request_parameters[$parameterHash['name']] = $parameterHash['value'];
        }

    }

    /**
     * @When /^I request "([^"]*)"$/
     */
    public function iRequest($arg1)
    {
        $this->response = $this->client->$arg1($this->request_parameters);

    }

    /**
     * @Then /^the response has a "([^"]*)" key$/
     */
    public function theResponseHasKey($key)
    {
        $data = $this->response;

        if (!empty($data)) {
            if (!isset($data[$key]  ))
                throw new Exception("Key '".$key."' is not set!\n");
        } else {
            throw new Exception("Response was not JSON\n");
        }

    }

}
