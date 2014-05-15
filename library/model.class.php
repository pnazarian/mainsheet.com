<?php

//called by the views

abstract class Model {
	
	//returns an Article object
	public static function getArticle($id)
	{
		$result = Database::queryArticle($id);
		
		if ($result === NULL) // article not found
			return false;
		
		$articleID = $result['articleID'];
		$title = $result['articleTitle'];
		$date = $result['date'];
		$section = $result['sectionName'];
		$isOnlineOnly = $result['volumeID']===NULL;
		$volumeID = $result['volumeID'];
		$issueNumber = $result['issueNumber'];
		$text = Helper::escapes2html($result['articleText']);
		$hits = $result['hits'];
		$keywords = explode(" ", trim($result['keywords']));
		$pageID = $result['pageID'];
		
		$results = Database::queryArticleAuthors($id);
		$authors = array();
		for ($i=0; $i < mysqli_num_rows($results); $i++)
		{
			$authors[$i] = mysqli_fetch_assoc($results)['authorName'];
		}
		
		$results = Database::queryArticleImages($id);
		$images = array();
		for ($i=0; $i < mysqli_num_rows($results); $i++)
		{
			$currentResult = mysqli_fetch_assoc($results);
			
			$images[$i] = new Image($currentResult['imageID'], $currentResult['credit'], $currentResult['caption']);
		}
		
		$results = Database::queryRelatedArticles($keywords, $id);
		
		$relatedArticles = array();
		
		for ($i=0; $i < mysqli_num_rows($results); $i++)
		{
			$currentResult = mysqli_fetch_assoc($results);
			
			$currentArticleID = $currentResult['articleID'];
			$currentTitle = $currentResult['articleTitle'];
			$currentDate = $currentResult['date'];
			$currentSection = $currentResult['sectionName'];
			$currentIsOnlineOnly = $currentResult['volumeID']===NULL;
			$currentVolumeID = $currentResult['volumeID'];
			$currentIssueNumber = $currentResult['issueNumber'];
			$currentPromotion = $currentResult['promotion'];
				
			$currentResults = Database::queryArticleAuthors($currentResult['articleID']);
			$currentAuthors = array();
			for ($j=0; $j < mysqli_num_rows($currentResults); $j++)
			{
				$currentAuthors[$j] = mysqli_fetch_assoc($currentResults)['authorName'];
			}
							
			$relatedArticles[$i] = new miniArticleListing($currentArticleID, $currentTitle, $currentDate, $currentAuthors, $currentSection, $currentIsOnlineOnly,
															$currentVolumeID, $currentIssueNumber, $currentPromotion);
		}
		
		return new Article($articleID, $title, $date, $authors, $section, $isOnlineOnly, $volumeID, $issueNumber, $text, $images, $hits, $keywords, $relatedArticles, $pageID);
	}
	
	//returns Issue[]
	public static function getIssues()
	{
		$results = Database::queryIssues();
		
		$issues = array();
		
		for ($i=0; $i<mysqli_num_rows($results); $i++)
		{
			$currentResult = mysqli_fetch_assoc($results);
			$issues[$i] = new Issue($currentResult['volumeID'], $currentResult['issueNumber'], $currentResult['issueDate']);
		}
		
		return $issues;
	}
	
	//returns ArticleListing[]
	public static function getLastIssueArticles()
	{
		$result = Database::queryLastIssue();
		
		$results = Database::queryArticleSearch(NULL, NULL, NULL, NULL, $result['volumeID'], $result['issueNumber'], 'sectiondescpromotiondesc');
		
		$out = array();
		for ($i = 0; $i<mysqli_num_rows($results); $i++)
		{
			$currentResult = mysqli_fetch_assoc($results);
			
			$articleID = $currentResult['articleID'];
			$title = $currentResult['articleTitle'];
			$date = $currentResult['date'];
			$section = $currentResult['sectionName'];
			$isOnlineOnly = $currentResult['volumeID']===NULL;
			$volumeID = $currentResult['volumeID'];
			$issueNumber = $currentResult['issueNumber'];
			$text = $currentResult['articleText'];
			$image = $currentResult['imageID']===NULL ? NULL : new ImageListing($currentResult['imageID']);
			$promotion = $currentResult['promotion'];
			$sectionID = $currentResult['sectionID'];
			
			$currentResults = Database::queryArticleAuthors($currentResult['articleID']);
			$authors = array();
			for ($j=0; $j < mysqli_num_rows($currentResults); $j++)
			{
				$authors[$j] = mysqli_fetch_assoc($currentResults)['authorName'];
			}
			
			$out[$i] = new ArticleListing($articleID, $title, $date, $authors, $section, $isOnlineOnly, $volumeID, $issueNumber, $text, $image, $promotion, $sectionID);
		}
		
		return $out;
	}
	
	//returns MiniArticleListing[];
	public static function getLastOnlineOnlyArticles($limit = 15)
	{
		$results = Database::queryArticleSearch(NULL, 'web', NULL, NULL, NULL, NULL, 'datedesc', 1, $limit);
		
		$out = array();
		for ($i=0; $i<mysqli_num_rows($results); $i++)
		{
			$currentResult = mysqli_fetch_assoc($results);
			
			$articleID = $currentResult['articleID'];
			$title = $currentResult['articleTitle'];
			$date = $currentResult['date'];
			$section = $currentResult['sectionName'];
			$promotion = $currentResult['promotion'];
			
			$currentResults = Database::queryArticleAuthors($articleID);
			$authors = array();
			for ($j=0; $j < mysqli_num_rows($currentResults); $j++)
			{
				$authors[$j] = mysqli_fetch_assoc($currentResults)['authorName'];
			}
			
			$out[$i] = new MiniArticleListing($articleID, $title, $date, $authors, $section, true, 0, 0, $promotion);
		}
		return $out;
	}
	
	//return array[pageNumber] = pageID
	public static function getIssuePages($volumeID, $issueNumber)
	{
		$results = Database::queryIssuePages($volumeID, $issueNumber);
		
		$out = array();
		
		for ($i=0; $i<mysqli_num_rows($results); $i++)
		{
			$currentResult = mysqli_fetch_assoc($results);
			
			$out[$currentResult['pageNumber']] = $currentResult['pageID'];
		}
		
		return $out;
	}
	
	//return Issue
	public static function getLastUnpublishedIssue()
	{
		$result = Database::queryLastIssue(true);
		
		if (!$result)
			return false;
		
		return new Issue($result['volumeID'], $result['issueNumber'], null, $result['googleFolderID'], $result['googlePageFolderID'], $result['googleImageFolderID']);
	}
	
	//return ArticleListing[]
	public static function getSearchArticles($terms, $media, $authorID, $sectionID, $volumeID, $issueNumber, $sortby)
	{
		$quoteStarts = substr($terms, 0, 1)=='"';
		$termsArray = explode('"', $terms);
		for ($i=0+$quoteStarts; $i<count($termsArray); $i+=2)
		{
			$explosion = explode(' ', $termsArray[$i]);
			$termsArray[$i] = $explosion[0];
			for ($j=1; $j<count($explosion); $j++)
				$termsArray[count($termsArray)] = $explosion[$j];
		}
		
		$results = Database::queryArticleSearch($termsArray, $media, $authorID, $sectionID, $volumeID, $issueNumber, $sortby);
		
		$out = array();
		for ($i=0; $i<mysqli_num_rows($results); $i++)
		{
			$currentResult = mysqli_fetch_assoc($results);
			
			$articleID = $currentResult['articleID'];
			$title = $currentResult['articleTitle'];
			$date = $currentResult['date'];
			$section = $currentResult['sectionName'];
			$isOnlineOnly = $currentResult['volumeID']===NULL;
			$volumeID = $currentResult['volumeID'];
			$issueNumber = $currentResult['issueNumber'];
			$text = $currentResult['articleText'];
			$image = $currentResult['imageID']===NULL ? NULL : new ImageListing($currentResult['imageID']);
			$promotion = $currentResult['promotion'];
			$sectionID = $currentResult['sectionID'];
			
			$authors = array();
			$currentResults = Database::queryArticleAuthors($articleID);
			for ($j=0; $j<mysqli_num_rows($currentResults); $j++)
			{
				$authors[$j] = mysqli_fetch_assoc($currentResults)['authorName'];
			}
			
			$out[$i] = new ArticleListing($articleID, $title, $date, $authors, $section, $isOnlineOnly, $volumeID, $issueNumber, $text, $image, $promotion, $sectionID);
		}
		
		return $out;
	}
	
	//return String[] of author names as authorID => authorName
	public static function getSearchAuthors($terms, $media, $sectionID, $volumeID, $issueNumber)
	{
		$quoteStarts = substr($terms, 0, 1)=='"';
		$termsArray = explode('"', $terms);
		for ($i=0+$quoteStarts; $i<count($termsArray); $i+=2)
		{
			$explosion = explode(' ', $termsArray[$i]);
			$termsArray[$i] = $explosion[0];
			for ($j=1; $j<count($explosion); $j++)
				$termsArray[count($termsArray)] = $explosion[$j];
		}
		
		$results = Database::queryArticleAuthorsSearch($termsArray, $media, $sectionID, $volumeID, $issueNumber);
		
		$authors = array();
		for ($i=0; $i<mysqli_num_rows($results); $i++)
		{
			$currentResult = mysqli_fetch_assoc($results);
			
			$authors[$currentResult['authorID']] = $currentResult['authorName'];
		}
		
		return $authors;
	}
	
	//returns String[] as sectionID => sectionName
	public static function getAllSections()
	{
		$results = Database::querySections();
		
		$sections = array();
		for ($i=0; $i<mysqli_num_rows($results); $i++)
		{
			$currentResult = mysqli_fetch_assoc($results);
			
			$sections[$currentResult['sectionID']] = $currentResult['sectionName'];
		}
		
		return $sections;
	}
	
	public static function getAllAuthors()
	{
		$results = Database::queryAuthors();
		
		$authors = array();
		for ($i=0; $i<mysqli_num_rows($results); $i++)
		{
			$currentResult = mysqli_fetch_assoc($results);
			
			$authors[$i] = new Author($currentResult['authorID'], $currentResult['authorName'], $currentResult['authorDisplayName']);
		}
		
		return $authors;
	}
	
	public static function getArticleFiles($folderID)
	{
		return GoogleDriveService::queryFiles($folderID, 'application/vnd.google-apps.document')->items;
	}
	
	public static function getPageFiles($folderID)
	{
		return GoogleDriveService::queryFiles($folderID, 'application/pdf')->items;
	}
	
	public static function getImageFiles($folderID)
	{
		return GoogleDriveService::queryFiles($folderID, 'image/jpeg')->items;
	}
}
?>