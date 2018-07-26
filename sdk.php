<?php

/**
 * Docxpresso SERVER SDK
 *
 * @copyright  Copyright (c) 2017 No-nonsense Labs (http://www.nononsenselabs.com)
 * @license    MIT
 * @link       https://opensource.org/licenses/MIT
 * @version    1.0
 * @since      1.0
 */

namespace SDK_Docxpresso;


/**
 * A collection of methods that simplify the data exchange and communication
 * with the Docxpresso SERVER package
 */

class Utils
{
    /**
     * Construct
     *
     * @param array $options with the following keys and values
     *      'pKey' => (string) the private key of your Docxpresso SERVER 
     *       installation
     *      'docxpressoInstallation' => (string) the URL of your Docxpresso
     *      SERVER installation
     * 
     * @access public
     */
    public function __construct($options = array())
    {
        $this->_options = $options;
    }

    /**
     * Checks the validity of an API key
     * 
     * @param string $key the key you wish to validate
     * @param string $data the string that was used to generate the key
     * @param string $pKey the private key used to generate the hash
     * @return boolean
     * @access public
     */
    public function apikey_control($key, $data, $pKey)
    {
        $resultbin =  self::sha1_hmac($pKey , $data);
        $result = bin2hex($resultbin);
        if ($key == $result) {
            return true;
        } else {
            return false;
        }
    }
  
    /**
     * Encodes in base64 url safe
     * 
     * @param string $str
     * @return string
     * @access public
     */
    public function base64_encode_url_safe($str)
    {
        return strtr(base64_encode($str), '+/=', '-_,');
    }
  
    /**
     * Decodes base64 url safe
     * 
     * @param string $str
     * @return string
     * @access public
     */
    public function base64_decode_url_safe($str)
    {
        return base64_decode(strtr($str, '-_,', '+/='));
    }
    
    /**
     * Generates a one time link to preview a document in the associated
     * Docxpresso SERVER interface
     * 
     * @param array $data with the following keys and values
     *      'template' => (int) the id of the requested document template.
     *       This value is compulsory and must correspond to a valid template
     *       id.
     *      'token' => (string) a unique identifier of a previous use. If given
     *       the requestDataURI option will be ignored and the data associated 
     *       with the token will be preloaded into the document.
     *      'identifier' => (string) optional var name that we may pass to help 
     *       identify that particular usage. Default value is an empty string 
     *      'reference' => (string) an optional string we may pass to help 
     *       identify that particular usage. Default value is an empty string
     *      'custom' => (string) an optional string we may pass to add external
     *       additional info to the template 
     *      'form' => (boolean) if true Docxpresso will serve a web form rather
     *       than an interactive document. Default value is false.
     *      'format' => (string) the requested document output format.
     *       Default value is "odt"· (Open Document Text).
     *      'enduserid' => (string) a value that will help us later to identify
     *       the user that requested the document. Default value is an empty 
     *       string.
     *      'requestConfigURI' => (string) the URL where Docxpresso should fetch
     *       external configuration adjustments.
     *      'requestDataURI' => (string) the URL where Docxpresso should fetch
     *       external data. Default value is an empty string.
     *       'responseExternalCSS' => (string) the URL where Docxpresso should
     *       fetch for some external CSS file.
     *      'responseExternalJS' => (string) the URL where Docxpresso should
     *       fetch for some external JS file.
     *      'responseDataURI' => (string) the URL where Docxpresso should
     *       forward the user data. Default value is an empty string.
     *      'responseURL' => (string) the URL where Docxpresso should redirect
     *       the end user after saving the data. Default value is an empty 
     *       string. 
     *      'documentName' => (string) the name we want to give to the generated
     *       document. Default value is an empty and in that case Docxpresso
     *       will use the default template name. 
     *       string.
     *      'domain' => (string) the URL doing the request. Default value is an 
     *       empty string.
     *      'prefix' => (string) a prefix that will limit enduser edition to
     *       only the field variables that start by that prefix. You can use
     *       a comma separated list to select mor than one prefix. Default value 
     *       is an empty string.
     *      'editableVars' => (string) a comma separated list of var names
     *       to restrict the edition to that set. Default value is an empty 
     *       string.
     *      'enforceValidation' => (boolean) if true the user will not be able
     *       to send data until all variable fields are validated. Default value
     *       is false.
     * @return string
     * @access public
     */
    public function previewDocument($data)
    {     
        if(!empty($data['form'])){
            $url = $this->_options['docxpressoInstallation'] . '/documents/previewForm/' . $data['template'];
        }else {
            $url = $this->_options['docxpressoInstallation'] . '/documents/preview/' . $data['template'];
        }
        $options = new \stdClass();
        if (isset($data['token'])) {
            $options->token = $data['token'];
        }
        if (isset($data['format'])) {
            $options->format = $data['format'];
        } else {
            $options->format = 'odt';
        }
        if (isset($data['enduserid'])) {
            $options->enduserid = $data['enduserid'];
        } 
        if (isset($data['identifier'])) {
            $options->identifier = $data['identifier'];
        }
        if (isset($data['reference'])) {
            $options->reference = $data['reference'];
        }
        if (isset($data['custom'])) {
            $options->custom = $data['custom'];
        }
        if (!empty($data['requestConfigURI'])) {
            $dURI = new \stdClass();
            $dURI->URL = $data['requestConfigURI'];
            $options->requestDataURI = json_encode($dURI);
        }
        if (!empty($data['requestDataURI'])) {
            $dURI = new \stdClass();
            $dURI->URL = $data['requestDataURI'];
            $dURI->requestData = 'preview';
            $options->requestDataURI = json_encode($dURI);
        }
        if (isset($data['requestExternalJS'])) {
            $options->requestExternalJS = $data['requestExternalJS'];
        }
        if (isset($data['requestExternalCSS'])) {
            $options->requestExternalCSS = $data['requestExternalCSS'];
        }
        if (!empty($data['responseDataURI'])) {
            $options->responseDataURI = $data['responseDataURI'];
        }
        if (!empty($data['responseURL'])) {
            $options->responseURL = $data['responseURL'];
        }
        if (!empty($data['documentName'])) {
            $options->documentName = $data['documentName'];
        }
        if (!empty($data['domain'])) {
            $options->domain = $data['domain'];
        }
        if (!empty($data['prefix'])) {
            $options->prefix = $data['prefix'];
        } 
        if (!empty($data['editableVars'])) {
            $options->editableVars = $data['editableVars'];
        } 
        if (!empty($data['enforceValidation'])) {
            $options->enforceValidation = true;
        }

        $opt = $this->base64_encode_url_safe(json_encode($options));
        
        return $this->_returnLink($url, $data['template'], $opt);    
    }
    
    /**
     * Generates a one time link to generate a document in the associated
     * Docxpresso SERVER interface
     * 
     * @param array $data with the following keys and values
     *      'template' => (int) the id of the requested document template.
     *       This value is compulsory and must correspond to a valid template
     *       id.
     *      'requestDataURI' => (string) the URL where Docxpresso should fetch
     *       external data. Default value is an empty string.
     *      'documentName' => (string) the name we want to give to the generated
     *       document (it should include the extensions: .odt, .pdf, .doc, 
     *       .doc(legacy), .docx or .rtf). The default values is document.odt
     *      'varData' => the JSON data we would like to use to generate the 
     *       document. This will only be used if the RequestDataURI parameter is
     *       empty
     *      'display' => (string) it can be 'document' (default) or 'form'. 
     *       This is only used for the generation of continue links
     *      'response' => (string) it can be 'download'(default) if the document
     *       is to be directly downloadable from the browser or json if we want
     *       to get the document as base64 encoded together with the usage id
     *       and token
     *      'callback' => it only spplies to json responses and sets the name
     *       of the callback function for JSONP responses.
     * @return string
     * @access public
     */
    public function requestDocument($data)
    {    
        $url = $this->_options['docxpressoInstallation'] . '/documents/requestDocument/' . $data['template'];
	
        $options = new \stdClass();
        if (isset($data['documentName'])) {
            $options->name = $data['documentName'];
        } else {
            $options->name = 'document.odt';
        }
        if (isset($data['varData'])) {
            $options->data = $data['varData'];
        } else {
            $options->data = '{}';
        }
        if (isset($data['display'])) {
            $options->display = $data['display'];
        } else {
            $options->display = 'document';
        }
        if (!empty($data['requestDataURI'])) {
            $dURI = new \stdClass();
            $dURI->URL = $data['requestDataURI'];
            $options->requestDataURI = json_encode($dURI);
        }
        

        $opt = $this->base64_encode_url_safe(json_encode($options));
        
        return $this->_returnLink($url, $data['template'], $opt);    
    }
    
    /**
     * Generates a one time link to download an attachment from the associated
     * Docxpresso SERVER installation
     * 
     * @param array $data $data with the following keys and values
     *      'usageId' => (int) the id of the corresponding usage.
     *       This value is compulsory and must correspond to a valid template
     *       id.
     *      'name' => (string) the name of the attachment file we want to
     *       download. It should correspond to the name given in the Docxpresso
     *       SERVER processing interface.
     *      'token' => (string) the token of the given usage for further
     *       security. 
     * @return string
     * @access public
     */
    public function downloadAttachment($data)
    {    
        $url = $this->_options['docxpressoInstallation']. '/documents/getAttachment/' . $data['usageId'];

        $uniqid = uniqid() . rand(99999, 9999999);
        $timestamp = time();

        $options = new \stdClass();
        $options->name = $data['name'];
        $options->token = $data['token'];
	
        $opt = $this->base64_encode_url_safe(json_encode($options));
      
        return $this->_returnLink($url, $data['usageId'], $opt);     
    }
    
    /**
     * Generates a one time link to download a "full· document package" in zip
     * format (document + attachments) from the associated
     * Docxpresso SERVER installation
     * 
     * @param array $data $data with the following keys and values
     *      'id' => (int) the id of the corresponding template.
     *       This value is compulsory and must correspond to a valid template
     *       id.
     *      'token' => (string) the token of the requested usage.
     * @return string
     * @access public
     */
    public function downloadDocument($data)
    {    
        $url = $this->_options['docxpressoInstallation']. '/documents/getFullDocumentation/' . $data['id'];

        $options = new \stdClass();
        $options->token = $data['token'];
        $opt = $this->base64_encode_url_safe(json_encode($options));
       
        return $this->_returnLink($url, $data['id'], $opt);
    }
    
    /**
     * Generates a one time link to get all annex document data: thumbnail, 
     * base64 encoded template odt file, etcetera.
     * 
     * @param integer $token the token of the requested annex
     * @param string $callback the callback name that we want to use for padded
     * JSON responses. If empty plain JSON will be returned.
     * @return string
     * @access public
     */
    public function getAnnexData($token, $callback = '')
    {    
        $url = $this->_options['docxpressoInstallation']. '/RESTservices/predefined/get_annex/' . $token;

        if (!empty($callback)) {
            $url .= '/' . $callback;
        }
		
        return $this->_returnLink($url);
    }
    
    /**
     * Generates a one time link to get all document template data: Docxpresso
     *  data, thumbnail, base64 encoded template odt file, etcetera.
     * 
     * @param integer $id the id of the required template
     * @param string $callback the callback name that we want to use for padded
     * JSON responses. If empty plain JSON will be returned.
     * @return string
     * @access public
     */
    public function getTemplateData($id, $callback = '')
    {    
        $url = $this->_options['docxpressoInstallation']. '/RESTservices/predefined/get_template/' . $id;

        if (!empty($callback)) {
            $url .= '/' . $callback;
        }
		
        return $this->_returnLink($url, $id);
    }
    
    /**
     * Generates a one time link to get just a template thumbnail
     * 
     * @param integer $id the id of the required template
     * @param string $callback the callback name that we want to use for padded
     * JSON responses. If empty plain JSON will be returned.
     * @return string
     * @access public
     */
    public function getTemplateThumbnail($id, $callback = '')
    {    
        $url = $this->_options['docxpressoInstallation']. '/RESTservices/predefined/get_thumbnail/' . $id;

        if (!empty($callback)) {
            $url .= '/' . $callback;
        }
		
        return $this->_returnLink($url, $id);
    }
    
    /**
     * Get usage history by year/month
     * 
     * @param string $callback the callback name that we want to use for padded
     * JSON responses. If empty plain JSON will be returned.
     * @return string
     * @access public
     */
    public function getUsageHistory($callback = '')
    {    
        $url = $this->_options['docxpressoInstallation']. '/RESTservices/predefined/get_usage_history';

        if (!empty($callback)) {
            $url .= '/' . $callback;
        }
		
        return $this->_returnLink($url);
    }
    
    /**
     * Allows to remotely authenticate from any other application into the
     * associated Docxpresso SERVER installation
     * 
     * @param array $data $data with the following keys and values
     *      'email' => (string) the email of the user we want to log in.
     *       This value is compulsory and must correspond to a valid registered
     *       user email.
     *      'url' => (string) target url where the user should be redirected
     *       after being authenticated
     * @return string
     * @access public
     */
    public function accessByTokenAction($data)
    {    
        $url = $this->_options['docxpressoInstallation']. '/users/accessByToken';

        $options = new \stdClass();
        $options->email = $data['email'];
        $options->url = $data['url'];

        $opt = $this->base64_encode_url_safe(json_encode($options));
      
        return $this->_returnLink($url, NULL, $opt);     
    }
    
    /**
     * Returns a link to list of categories in JSON(P) format from the associated
     * Docxpresso SERVER installation
     * 
     * @param string $callback the callback name that we want to use for padded
     * JSON responses. If empty plain JSON will be returned.
     * @return string
     * @access public
     */
    public function listCategories($callback = '')
    {    
        $url = $this->_options['docxpressoInstallation']. '/RESTservices/predefined/categories';

        if (!empty($callback)) {
            $url .= '/' . $callback;
        }
       
        return $this->_returnLink($url);
    }
    
    /**
     * Returns a link to list of documents in a given category in JSON(P) format
     * from the associated Docxpresso SERVER installation
     * 
     * @param integer $category the corresponding category id.
     * @param string $callback the callback name that we want to use for padded
     * JSON responses. If empty plain JSON will be returned.
     * @param boolean $published if true only "published" templates will be
     * available through the request.
     * @param string $access it can be "all" (deafult value), "public" for only
     * documents declared like public or a "username" to filter by permissions
     * @return string
     * @access public
     */
    public function documentsByCategory($category, $callback = '', $published = 0, $access = 'all')
    {    
        $url = $this->_options['docxpressoInstallation']. '/RESTservices/predefined/documents_by_category/' . $category;

        if (!empty($callback)) {
            $url .= '/' . $callback;
        } else {
            $url .= '/NULL';
        }

        if (!empty($published)) {
            $url .= '/1';
        } else {
            $url .= '/0';
        }
        
        if (!empty($access)) {
            $url .= '/' . rawurlencode($access);
        }
		
        return $this->_returnLink($url);
    }
    
    /**
     * Allows to change the password associated with a user email.
     * 
     * @param string $email user unique email identifier.
     * @param string $password new password. It should be, at least 8 chars long
     * and contain at least an uppercase letter, a lowercase letter, a number
     * and a non-standard char: !,%,&,@,#,$,^,*,?,_,~
     * @param string $callback the callback name that we want to use for padded
     * JSON responses. If empty plain JSON will be returned.
     * @return string
     * @access public
     */
    public function modifyPassword($email, $password, $callback = '')
    {    
        $url = $this->_options['docxpressoInstallation']. '/RESTservices/predefined/modify_password';

        if (!empty($callback)) {
            $url .= '/' . $callback;
        }
	$options = new \stdClass();
        $options->email = $email;
        $options->password = $password;

        $opt = $this->base64_encode_url_safe(json_encode($options));
        return $this->_returnLink($url, NULL, $opt);
    }
    
    /**
     * Allows to modify the configuration of Signature Providers. If the
     * requested signature provider does not exist and it belongs to one of
     * the current available ones the corresponding entry will be cerated.
     * 
     * @param string $provider the name of the signature provider. Currently
     * the only available one is vidSigner
     * @param string $config base64 encoded config JSON.
     * @param string $callback the callback name that we want to use for padded
     * JSON responses. If empty plain JSON will be returned.
     * @return string
     * @access public
     */
    public function modifySignatureProvider($provider, $config, $callback = '')
    {    
        $url = $this->_options['docxpressoInstallation']. '/RESTservices/predefined/modify_signature_providers/' . $provider;

        if (!empty($callback)) {
            $url .= '/' . $callback;
        }
	$options = new \stdClass();
        $options->custom = $config;

        $opt = $this->base64_encode_url_safe(json_encode($options));
        return $this->_returnLink($url, NULL, $opt);
    }
    
    /**
     * Allows to delete a current signature provider.
     * 
     * @param string $provider the name of the signature provider. Currently
     * the only available one is vidSigner
     * @param string $callback the callback name that we want to use for padded
     * JSON responses. If empty plain JSON will be returned.
     * @param boolean $published if true only "published" templates will be
     * available through the request.
     * @return string
     * @access public
     */
    public function deleteSignatureProvider($provider, $callback = '')
    {    
        $url = $this->_options['docxpressoInstallation']. '/RESTservices/predefined/delete_signature_providers/' . $provider;

        return $this->_returnLink($url);
    }
    
    /**
     * List all Signature Providers
     * 
     * @param string $callback the callback name that we want to use for padded
     * JSON responses. If empty plain JSON will be returned.
     * @param boolean $published if true only "published" templates will be
     * available through the request.
     * @return string
     * @access public
     */
    public function listSignatureProviders($callback = '')
    {    
        $url = $this->_options['docxpressoInstallation']. '/RESTservices/predefined/list_signature_providers';

        if (!empty($callback)) {
            $url .= '/' . $callback;
        }
		
        return $this->_returnLink($url);
    }
    
    /**
     * Returns a link to download the whole document (sub)tree in JSON(P) format  
     * from the associated Docxpresso SERVER installation
     * 
     * @param mixed $rootCategory the corresponding category id from which
     * we want to build the document tree. Default value is 'root' that corresponds
     * with the "root category"
     * @param string $callback the callback name that we want to use for padded
     * JSON responses. If empty plain JSON will be returned.
     * @param boolean $published if true only "published" templates will be
     * available through the request.
     * @param string $access it can be "all" (deafult value), "public" for only
     * documents declared like public or a "username" to filter by permissions
     * @return string
     * @access public
     */

    public function documentTree($rootCategory= 'root', $callback = '', $published = 0, $access = 'all')
    {    
        if ($rootCategory == 'root') {
            $rootCategory = 1;
        }
        
        $url = $this->_options['docxpressoInstallation']. '/RESTservices/predefined/document_tree/' . $rootCategory;

        if (!empty($callback)) {
            $url .= '/' . $callback;
        } else {
            $url .= '/NULL';
        }

        if (!empty($published)) {
            $url .= '/1';
        } else {
            $url .= '/0';
        }
        
        if (!empty($access)) {
            $url .= '/' . rawurlencode($access);
        }

        return $this->_returnLink($url);
    }
    
    /**
     * Returns a link to download all documents with a given name in JSON(P)  
     * format from the associated Docxpresso SERVER installation
     * 
     * @param string $name the name of the template we are looking for. This
     * method launches a "LIKE" SQL query.
     * @param string $callback the callback name that we want to use for padded
     * JSON responses. If empty plain JSON will be returned.
     * @param boolean $published if true only "published" templates will be
     * available through the request.
     * @param string $access it can be "all" (deafult value), "public" for only
     * documents declared like public or a "username" to filter by permissions
     * @return string
     * @access public
     */
    public function templatesByName($name, $callback = '', $published = 0, $access = "all")
    {    
        $url = $this->_options['docxpressoInstallation'];
        $url .= '/RESTservices/predefined/documents_by_name/' . rawurlencode($name);

        if (!empty($callback)) {
            $url .= '/' . $callback;
        } else {
            $url .= '/NULL';
        }

        if (!empty($published)) {
            $url .= '/1';
        } else {
            $url .= '/0';
        }
        
        if (!empty($access)) {
            $url .= '/' . rawurlencode($access);
        }

        return $this->_returnLink($url);
    }
	
    /**
     * Returns a link to download all template usage data in JSON(P)  
     * format  for a given template id from the associated Docxpresso 
     * SERVER installation
     * 
     * @param array $data with the following keys and values
     *      'id' => (int) the id of the template.
     *       This value is compulsory and must correspond to a valid template
     *       id.
     *      'identifier' => (string) the identifier field of an usage. The
     *       default value is an empty string
     *      'reference' => (string) the reference field of an usage. The
     *       default value is an empty string
     *      'enduserid' => (string) the end user id of a particular usage.
     *       Default value is an empty string.
     *      'startDate' => (string) a date in the format yyyy-mm-dd that will
     *       select usages that happened after it. Default value is an empty 
     *       string.
     *      'endDate' => (string) a date in the format yyyy-mm-dd that will
     *       select usages that happened before it. Default value is an empty 
     *       string.
     *      'locked' => (integer) it can be zero for all usages (default), 1 if
     *       we only want usages that have been set as completed or 2 for the 
     *       opposite.
     *      'firstResult' => (int) query offset. Default value is 0; 
     *      'maxResults' => (int) maximum number of results. Beware that
     *       each installation may have upper limits to this number.
     *       Default value is an empty and in that case Docxpresso
     *      'sort' => (string) teh field used to sort the results.
     *      'order' => (string) possible values are DESC (default) or ASC.
     * @param string $callback the callback name that we want to use for padded
     * JSON responses. If empty plain JSON will be returned.
     * @return string
     * @access public
     */
    public function dataByTemplate($data, $callback = '')
    {    
        $url = $this->_options['docxpressoInstallation']. '/RESTservices/predefined/data_by_template/' . $data['id'];
		
        if (!empty($callback)) {
                $url .= '/' . $callback;
        }

        //we build and options object with the search filters
        $options = new \stdClass();
        if (!empty($data['identifier'])) {
            $options->identifier = $data['identifier'];
        }
        if (!empty($data['reference'])) {
            $options->reference = $data['reference'];
        }
        if (!empty($data['enduserid'])) {
            $options->enduserid = $data['enduserid'];
        }
        if (!empty($data['locked'])) {
            $options->locked = $data['locked'];
        }
        //dates must be in the format 2016-01-30
        if (!empty($data['startDate'])) {
            $options->startDate = $data['startDate'];
        }
        if (!empty($data['endDate'])) {
            $options->endDate = $data['endDate'];
        }
        if (!empty($data['firstResult'])) {
            $options->firstResult = $data['firstResult'];
        }
        if (!empty($data['maxResults'])) {
            $options->maxResults = $data['maxResults'];
        }
        if (!empty($data['sort'])) {
            $options->sort = $data['sort'];
        }
        if (!empty($data->order)) {
            $options->order = $data['order'];
        }

        $opt = $this->base64_encode_url_safe(json_encode($options));
		
        return $this->_returnLink($url, $data['id'], $opt);
    }
    
    /**
     * Returns a link to download the data of a given single usage JSON(P)  
     * format from the associated Docxpresso SERVER installation
     * 
     * @param integer $usageId the id of a particular usage
     * @param string $callback the callback name that we want to use for padded
     * JSON responses. If empty plain JSON will be returned.
     * @return string
     * @access public
     */
    public function dataByUsage($usageId, $callback = '')
    {    
        $url = $this->_options['docxpressoInstallation']. '/RESTservices/predefined/data_by_usage/' . $usageId;

        if (!empty($callback)) {
            $url .= '/' . $callback;
        }
		
        return $this->_returnLink($url, $usageId);
    }
    
    /**
     * Returns a link to generate a HTML or CSV file for all the data usage
     * for a given template  
     * 
     * @param array $data with the following keys and values
     *      'id' => (int) the id of the template. This value is compulsory and 
     *       must correspond to a valid template id.
     *      'format' => (string) it may be html (default) or csv
     *      'identifier' => (string) the identifier field of an usage. The
     *       default value is an empty string
     *      'reference' => (string) the reference field of an usage. The
     *       default value is an empty string
     *      'enduserid' => (string) the end user id of a particular usage.
     *       Default value is an empty string.
     *      'after' => (string) a date in the format yyyy-mm-dd that will
     *       select usages that happened after it. Default value is an empty 
     *       string.
     *      'before' => (string) a date in the format yyyy-mm-dd that will
     *       select usages that happened before it. Default value is an empty 
     *       string.
     *      'firstResult' => (int) query offset. Default value is 0; 
     *      'maxResults' => (int) maximum number of results. Beware that
     *       each installation may have upper limits to this number.
     *       Default value is an empty and in that case Docxpresso
     *      'sort' => (string) teh field used to sort the results.
     *      'order' => (string) possible values are DESC (default) or ASC.
     * @return string
     * @access public
     */
    public function dataDigestByUsage($data)
    {    
        $url = $this->_options['docxpressoInstallation']. '/data/digest/' . $data['id'] ;

        $url = $this->_returnLink($url, $data['id'], NULL) . '&';

        //we build the URL with the search filters and output foemat
        if (!empty($data['format'])) {
            $url .= 'format=' . $data['format'] . '&';
        }
        if (!empty($data['identifier'])) {
            $url .= 'identifier=' . $data['identifier'] . '&';
        }
        if (!empty($data['reference'])) {
            $url .= 'reference=' . $data['reference'] . '&';
        }
        if (!empty($data['enduserid'])) {
            $url .= 'enduserid=' . $data['enduserid'] . '&';
        }
        //dates like before and after must be in the format 2016-01-30
        if (!empty($data['before'])) {
            $url .= 'before=' . $data['before'] . '&';
        }
        if (!empty($data['after'])) {
            $url .= 'after=' . $data['after'] . '&';
        }
        if (!empty($data['domain'])) {
            $url .= 'domain=' . $data['domain'] . '&';
        }
        if (!empty($data['maxResults'])) {
            $url .= 'maxResults=' . $data['maxResults'] . '&';
        }
        if (!empty($data['sort'])) {
            $url .= 'sort=' . $data['sort'] . '&';
        }
        if (!empty($data->order)) {
            $url .= 'order=' . $data['order'];
        }

        return $url;
    }
    
    /**
     * Returns a link to get a JSON with both the document base64 encoded 
     * together with the other usage data 
     * 
     * @param integer $usageId the id of a particular usage
     * @param string $callback the callback name that we want to use for padded
     * JSON responses. If empty plain JSON will be returned.
     * @return string
     * @access public
     */
    public function documentAndDataByUsage($usageId, $callback = '')
    {    
        $url = $this->_options['docxpressoInstallation']. '/RESTservices/predefined/document_and_data_by_usage/' . $usageId;

        if (!empty($callback)) {
            $url .= '/' . $callback;
        }
		
        return $this->_returnLink($url, $usageId);
    }
    
    /**
     * Returns a link to download basic statistical data like number of uses
     * and last usage
     * 
     * @param mixed $id template id. If set to 'all' the data
     * for all available templates will be provided
     * @param string $callback the callback name that we want to use for padded
     * JSON responses. If empty or NULL plain JSON will be returned.
     * @param boolean $published if true only "published" templates will be
     * available through the request.
     * @return string
     * @access public
     */
    
    public function dataStatistics($id = 'all', $callback = '', $published = 0)
    {    
        $url = $this->_options['docxpressoInstallation']. '/RESTservices/predefined/data_statistics';
		
        if (!empty($id)) {
            $url .= '/' . $id;
        } else {
            $url .= '/all';
        }
		
        if (!empty($callback)) {
            $url .= '/' . $callback;
        } else if (!empty($published)) {
            $url .= '/NULL';
        }

        if (!empty($published)) {
            $url .= '/1';
        }

        return $this->_returnLink($url, $id);
    }
    
    /**
     * Returns a link to download the total usage count group by day 
     * 
     * @param array $data with the following keys and values
     *      'id' => (mixed) the id of the template. If set to 'all' the data
     *       for all available templates will be provided.
     *      'after' => (string) a date in the format yyyy-mm-dd that will
     *       select usages that happened after it. Default value is an empty 
     *       string.
     *      'before' => (string) a date in the format yyyy-mm-dd that will
     *       select usages that happened before it. Default value is an empty 
     *       string.
     * @param string $callback the callback name that we want to use for padded
     * JSON responses. If empty or NULL plain JSON will be returned.
     * @return string
     * @access public
     */
    public function usageCount($data = array(), $callback = '')
    {    
        if (!isset($data['id'])) {
            $data['id'] = 'all';
        }
        $url = $this->_options['docxpressoInstallation']. '/RESTservices/predefined/usage_count/' . $data['id'];
		
        if (!empty($callback)) {
            $url .= '/' . $callback;
        }

        //we build and options object with the search filters
        $options = new \stdClass();
        if (!empty($data['before'])) {
            $options->before = $data['before'];
        }
        if (!empty($data['after'])) {
            $options->after = $data['after'];
        }

        $opt = $this->base64_encode_url_safe(json_encode($options));
		
        return $this->_returnLink($url, $data['id'], $opt);
    }
    
    /**
     * Returns a link to list of users in JSON(P) format from the associated
     * Docxpresso SERVER installation
     * 
     * @param string $callback the callback name that we want to use for padded
     * JSON responses. If empty plain JSON will be returned.
     * @return string
     * @access public
     */
    public function userList($callback = '')
    {    
        $url = $this->_options['docxpressoInstallation']. '/RESTservices/predefined/users';

        if (!empty($callback)) {
            $url .= '/' . $callback;
        }
       
        return $this->_returnLink($url);
    }
    
    /**
     * Creates the link requested by all other methods
     * 
     * @param string $url
     * @param mixed $id
     * @param mixed $opt
     * @return string
     * @access private
     */
    private function _returnLink($url, $id = NULL, $opt = NULL)
    {
        $uniqid = uniqid() . rand(99999, 9999999);
        $timestamp = time();
	
        $control = '';
        if (!empty($id)){
            $control .=  $id . '-';
        } 
        $control .= $timestamp . '-' . $uniqid;

        if (!empty($opt)){
            $control .= '-' . $opt;
        } 

        $dataKey = sha1($control, true);
        $masterKey = $this->_options['pKey'];
        $APIKEY = bin2hex($this->sha1_hmac($masterKey, $dataKey));

        //we should now redirect to Docxpresso
        $addr = $url . '?';
        $addr .= 'uniqid=' . $uniqid .'&';
        $addr .= 'timestamp=' . $timestamp . '&';
        $addr .= 'APIKEY=' . $APIKEY;

        if(!empty($opt)){
            $addr.= '&options=' . $opt;
        }
		
        return $addr;
    }


    /**
     * generates hmac with sha1
     * 
     * This method has been taken from the Wikipedia:
     * https://en.wikipedia.org/wiki/Hash-based_message_authentication_code
     * This content is released under CC-BY-SA:
     * http://creativecommons.org/licenses/by-sa/3.0/
     * 
     */
    private function sha1_hmac($key,$data,$blockSize=64,$opad=0x5c,$ipad=0x36) 
    {
        // Keys longer than blocksize are shortened
        if (strlen($key) > $blockSize) {
            $key = sha1($key,true);	
        }

        // Keys shorter than blocksize are right, zero-padded (concatenated)
        $key = str_pad($key,$blockSize,chr(0x00),STR_PAD_RIGHT);	
        $o_key_pad = $i_key_pad = '';

        for($i = 0;$i < $blockSize;$i++) {
            $o_key_pad .= chr(ord(substr($key,$i,1)) ^ $opad);
            $i_key_pad .= chr(ord(substr($key,$i,1)) ^ $ipad);
        }

        return sha1($o_key_pad.sha1($i_key_pad.$data,true),true);
    }
    
    /*********************************************/
    /*               ADMIN METHODS               */
    /* Only for use of platform administrators   */
    /*********************************************/ 

    /**
     * Returns a link to modify the corresponding instance config
     * This method is only accessible to the platform admins 
     * 
     * @param string $secret private key only accesible to paltform admins
     * @param array $data with the following optional keys and values 
     *      'isActive' => (boolean) determines if the docxpresso installation
     *       is active or not
     *      'trusted' => (boolean) determines if the docxpresso installation
     *       should be trusted to run custom JS
     *      'plan' => (string) changes the associates instance plan
     *      'policy' => (string) json encoded object including the policy
     *       associated with the assigned plan.
     *      'usageLimits' => (string) json encoded object including the limits
     *       in the generation of documents for the assigned plan.
     *      'batch' => (string) json encoded object including the policy
     *       for the batch jobs associated with the current plan.
     *      'analyticsRunSize' => (integer) max size of a custom analytics run.
     * @return string
     * @access public
     */
    public function modifyConfig($secret, $data = array())
    {    
        //additional security
        $masterKey = $this->_options['pKey'];
        $secret = bin2hex($this->sha1_hmac($masterKey, $secret));
        
        $url = $this->_options['docxpressoInstallation']. '/RESTservices/predefined/modify_config';

        //we build and options object with the search filters
        $options = new \stdClass();
        $options->secret = $secret;
        if (isset($data['isActive'])) {
            $options->isActive = $data['isActive'];
        }
        if (isset($data['trusted'])) {
            $options->trusted = $data['trusted'];
        }
        if (!empty($data['plan'])) {
            $options->plan = $data['plan'];
        }
        if (!empty($data['policy'])) {
            $options->policy = $data['policy'];
        }
        if (!empty($data['usageLimits'])) {
            $options->usageLimits = $data['usageLimits'];
        }
        if (!empty($data['batch'])) {
            $options->batch = $data['batch'];
        }
        if (!empty($data['analyticsRunSize'])) {
            $options->analyticsRunSize = $data['analyticsRunSize'];
        }

        $opt = $this->base64_encode_url_safe(json_encode($options));
		
        return $this->_returnLink($url, NULL, $opt);  
    }
    
    /**
     * Returns a link to obtain the current configuratin and history data 
     * 
     * @param string $secret private key only accesible to paltform admins
     * 
     * @return string
     * @access public
     */
    public function getConfig($secret)
    {    
        //additional security
        $masterKey = $this->_options['pKey'];
        $secret = bin2hex($this->sha1_hmac($masterKey, $secret));
        
        $url = $this->_options['docxpressoInstallation']. '/RESTservices/predefined/get_config/' . $secret;
		
        return $this->_returnLink($url);  
    }
}
