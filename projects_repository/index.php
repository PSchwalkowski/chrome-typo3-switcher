<?php
/**
 * BE/FE/Env Handy Switcher (TYPO3 dedicated, but is kind of universal)
 * Great help for integrators with many web projects, that runs on multiple paralell environments/contexts.
 * 
 * wolo.pl '.' studio
 * 2017-2020
 * wolo.wolski(at)gmail.com
 * http://wolo.pl/
 * 
 * https://chrome.google.com/webstore/detail/typo3-backend-frontend-ha/ohemimdlihjdeacgbccdkafckackmcmn
 * https://github.com/w010/chrome-typo3-switcher
 * 
 * 
 *
 * This component here is a simple (but fully functional - in basis) draft of Project Repository serverside.
 * The idea is to centralize/keep in-sync company's webprojects urls, to multiple working environments of each,
 * to make sure every developer in team when starting work on a project has always full set of latest and checked set
 * of urls to projects and its servers / instances / stages to lightspeed jumping between them only swapping the domain
 * within one click, without even need to configure your common projects manually to use. Just wait for someone else 
 * do it and fetch ready config ;)
 * 
 * (Ok, maybe it kinda sounds like "wtf, why do I even need something like that, when I can make a bookmark
 * on a browser bar like I do for years" but believe me or not, if you maintain dozens of multidomain webprojects on 
 * automated parallel environments on everyday basis, do every task on different one of them, then integrate changes
 * one-by-one on every stage server and every client has different naming conventions, this one changed domains recently,
 * that one broke subdomains, the other one never documents anything, and this one over there thinks you remember all
 * of urls to all of important-only-to-him subpages on dev, because you worked there, once, for an hour, three months ago...
 * After a few weeks using this you probably won't imagine your work without this plugin anymore.)
 * 
 * You can start to use this script as-is just by putting it somewhere on your webserver (rather http auth protected),
 * upload projects data (as json files, like these exported from Chrome plugin) into /data directory and that's it,
 * basically ready to fetch by your teammates.
 * It may be a good base to write something better, if you need. Ie. as Typo3 plugin and records, using fe_user
 * authentication, or so. (That one might be helpful when I finish push-config-to-repo functionality.)  
 */


/**
 * Class ProjectsRepository
 */
class ProjectsRepository    {
	
	protected $dataDir = 'data';


	public function __construct() {
		
	}
	
	public function handleRequest()  {
	
		switch ($_GET['action']) {
			case 'fetch':
				$this->sendContent('{
					"success": true,
					"result": [
						' . $this->prepareProjectsToOutput() . '
					]
				}');
			
				break;

			case 'push':
				die('not implemented yet');
			
				break;

			default:
				$this->sendContent('{
					"success": false,
					"result": [],
					"code": "action_not_found",
					"error": "Action not found or unavailable"
				}');
		}
	}


	/**
	 * Read projects and build output. Since they are json files already, glue them together into one json array
	 */
	protected function prepareProjectsToOutput() {
		
		$filter = $_GET['filter'];
		
		$json = '';
		if (is_dir($this->dataDir)){
	        foreach (scandir($this->dataDir) as $file)   {
	        	
	            if (substr($file, 0, 1) != '.'  &&  substr($file, -5, 5) == '.json') {
		            // read file content
		            $fileContent = file_get_contents($this->dataDir . '/' . $file);
		            if ($filter)    {
		            	// parse json and search for string occurance in name
			            $projectParsed = @json_decode($fileContent, true);
			            if (is_array($projectParsed))   {
			            	if (stristr($projectParsed['name'], $filter))    {
		                        $json .= $fileContent.',';
				            }
			            	else    {
			            		foreach (array_merge((array)$projectParsed['contexts'], (array)$projectParsed['links']) as $testItem)  {
			            			if (stristr($testItem['name'], $filter))    {
				                        $json .= $fileContent.',';
				                        break;
						            }
					            }
				            }
			            }
		            }
		            else    {
		                $json .= $fileContent.',';
		            }
	            }
		    }
		}
		// strip last comma
		$json = substr($json, 0, -1);
		
		return $json;
	}


	/**
	 * Output xhr body
	 * @param string $content
	 */
	protected function sendContent($content) {
		header('Content-type:application/json;charset=utf-8');
		print $content;
		exit;
	}
}


$ProjectsRepository = new ProjectsRepository();
$ProjectsRepository->handleRequest();
