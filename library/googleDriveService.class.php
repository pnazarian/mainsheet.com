<?php

class GoogleDriveService extends Singleton
{
	private $service;
	
	protected function __construct()
	{
		$client = GoogleClient::authenticate();
	
		$this->service = new Google_DriveService($client);	
	}
	
	protected function createIssueFolderSystem($volumeID, $issueNumber, $secret = false)
	{
		$folder = new Google_DriveFile();
		$folder->setTitle('Mainsheet: Vol '.$volumeID.' Num '.$issueNumber);
		$folder->setDescription('Created by the Mainsheet Website');
		$folder->setMimeType('application/vnd.google-apps.folder');
		$folder->setWritersCanShare('false');

		$mainFolder = $this->service->files->insert($folder, array(
						  'mimeType' => 'application/vnd.google-apps.folder',
						));

		$folder->setTitle('Articles - Docs');
		$folder->setParents(array($mainFolder));

		$articlesFolder = $this->service->files->insert($folder, array(
						'mimeType' => 'application/vnd.google-apps.folder',
						));
						
		$folder->setTitle('Pages - PDFs');

		$pagesFolder = $this->service->files->insert($folder, array(
						'mimeType' => 'application/vnd.google-apps.folder',
						));
						
		$folder->setTitle('Images - JPEGs');

		$imagesFolder = $this->service->files->insert($folder, array(
						'mimeType' => 'application/vnd.google-apps.folder',
						));
		
		if (!$secret)
		{
			$newPermission = new Google_Permission();
			$newPermission->setValue('pnazarian@chadwickschool.org');
			$newPermission->setType('user');
			$newPermission->setRole('reader');					//can only view folder, not edit it or its contents (unless additional permissions are applied, as they are below)

			$params = array('sendNotificationEmails' => false);

			$this->service->permissions->insert($mainFolder->id, $newPermission); // one notification email is sent for this folder only

			$newPermission->setRole('writer'); 					  //can edit contents of folder, but, unlike 'owner', cannot edit the folder itself
			$this->service->permissions->insert($articlesFolder->id, $newPermission, $params); // DO NOT SEND NOTIFICATION EMAIL FOR THESE: NO SPAM
			$this->service->permissions->insert($imagesFolder->id, $newPermission, $params);
			$this->service->permissions->insert($pagesFolder->id, $newPermission, $params);
		}
		
		return new IssueFolderSystem($articlesFolder->id, $pagesFolder->id, $imagesFolder->id);
	}
	
	protected function queryFiles($parentID, $mimeType)
	{	
		$parameters = array();
		$parameters['q'] = "'".$parentID."' in parents and trashed=false and mimeType='".$mimeType."'";
		
		return $this->service->files->listFiles($parameters);
	}
	
	protected function queryFileBody($id, $plainText = true)
	{
		if ($plainText)
			$downloadUrl = $this->service->files->get($id)->exportLinks['text/plain'];
		else
			$downloadUrl = $this->service->files->get($id)->getDownloadUrl();
		
		$request = new Google_HttpRequest($downloadUrl, 'GET', null, null);
		$httpRequest = Google_Client::$io->authenticatedRequest($request);
		
		return $httpRequest->getResponseBody();
	}
	
	protected function archiveFile($id, $isArticle = true)
	{
		if ($isArticle)
			$archiveFolderID = '0B99bsttQVKIeV0lzNk9MdXZHVXc'; //article folder
		else
			$archiveImageFolderID = '0B99bsttQVKIebHZPUVM4VlZncU0'; //image folder
		
		$moveFile = new Google_DriveFile();
		$moveFile->setParents(array($this->service->files->get($archiveFolderID)));
		
		$this->service->files->patch($id, $moveFile,  array(
		  'fields' => 'parents'
		));
	}
}

?>