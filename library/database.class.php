<?php

//called by Model

class Database extends Singleton {

	private $db;
	
	protected function __construct()
	{
		$this->db = @mysqli_connect('localhost', 'mainsheetcom', 'iNvErNo22', 'mainsheet');
	}
	
	private function relevanceQuery($termsArray)
	{
		$query = 'articles.articleID AS father, (0 + ';
		foreach ($termsArray as $term)
		{
			$term = Helper::alphaParse($term, true);
			if ($term != '' && !IgnoreWordList::isIgnoreWord($term))
			{
				$query .= "(keywords LIKE '%$term%')*6 +
						   (articleTitle LIKE '%$term%')*3 +
						   (articleText LIKE '%$term%')*1 +
						   (SELECT SUM(0 + (authorFirstName LIKE '%$term')*1 +
						   (authorLastName LIKE '%$term%')*1)
						   FROM article_authors, authors
						   WHERE articleID = father AND articles.articleID = article_authors.articleID AND article_authors.authorID = authors.authorID
						   ) +";
			}
		}
		
		return substr($query, 0, strlen($query)-2).' )';
	}
	
	protected function queryArticle($articleID)
	{
		$query = 'SELECT sectionName, articleTitle, date, articles.volumeID, articles.issueNumber, articleText, hits, keywords, articleID, promotion, 
		(SELECT pageID FROM pages WHERE articles.volumeID=pages.volumeID AND articles.issueNumber=pages.issueNumber AND articles.pageNumber=pages.pageNumber) as pageID
		from articles, sections WHERE
		articles.sectionID = sections.sectionID AND articles.articleID = '.$articleID;
		
		$results = mysqli_query($this->db, $query);
		
		return mysqli_num_rows($results)==0 ? NULL : mysqli_fetch_assoc($results);
	}
	
	protected function incrementArticleHits($articleID)
	{
		$query = 'UPDATE articles SET hits=hits+1 WHERE articles.articleID = '.$articleID;
		
		mysqli_query($this->db, $query);		
	}
	
	protected function queryArticleAuthors($articleID)
	{
		$query = 'SELECT authors.authorID, concat(authorFirstName, " ", authorLastName) AS authorName
		FROM article_authors, authors WHERE article_authors.authorID = authors.authorID
		AND articleID = '.$articleID;
		
		return mysqli_query($this->db, $query);	
	}
	
	protected function queryArticleImages($articleID)
	{
		$query = 'select images.imageID, caption, credit
		  from article_images, images where
		  article_images.imageID = images.imageID
		  AND articleID = '.$articleID;
					
		return mysqli_query($this->db, $query);
	}
	
	protected function queryRelatedArticles($keywordArray, $articleID = 0)
	{
		$query = 'SELECT articleTitle, date, sectionName, volumeID, articles.articleID, issueNumber, promotion, '.$this->relevanceQuery($keywordArray).
					' AS relevance FROM articles, sections WHERE 
					articles.sectionID = sections.sectionID
					AND articles.articleID != '.$articleID.'
					HAVING relevance>0 
					ORDER BY relevance DESC, hits DESC
					LIMIT 10';
		
		return mysqli_query($this->db, $query);
	}
	
	protected function queryIssues($published = true)
	{
		$query = 'SELECT * FROM issues WHERE issueDate IS '.($published ? 'NOT ' : '').'NULL ORDER BY volumeID DESC, issueNumber ASC';
		
		return mysqli_query($this->db, $query);
	}
	
	protected function queryIssue($volumeID, $issueNumber)
	{
		$query = 'SELECT * FROM issues WHERE volumeID='.$volumeID.' AND issueNumber='.$issueNumber;
					
		$results = mysqli_query($this->db, $query);
		
		return mysqli_num_rows($results)==0 ? NULL : mysqli_fetch_assoc($results);
	}
	
	protected function queryAuthors()
	{
		$query = 'SELECT authorID, concat(authorLastName, ", ", authorFirstName) AS authorDisplayName,
		concat(authorFirstName, " ", authorLastName) as authorName FROM authors ORDER BY authorDisplayName';
		
		return mysqli_query($this->db, $query);
	}
	
	protected function querySections()
	{
		$query = 'SELECT * FROM sections';
		
		return mysqli_query($this->db, $query);
	}
	
	protected function queryLastIssue($unpublished = false)
	{
		if (!$unpublished)
		{
			$query = 'SELECT * FROM issues ORDER BY issueDate DESC LIMIT 1'; // will automatically ignore unpublished issues
		} else {
			$query = 'SELECT * FROM issues WHERE issueDate IS NULL ORDER BY volumeID DESC, issueNumber DESC LIMIT 1';
		}
		
		$results = mysqli_query($this->db, $query);
		
		if (mysqli_num_rows($results)==0)
			return false;
		
		return mysqli_fetch_assoc($results);
	}
	
	protected function queryArticleSearch($termsArray = NULL, $media = '', $authorID = 0, $sectionID = '', $volumeID = 0, $issueNumber = 0, $sortby = '')
	{
		$query = 'SELECT articles.articleTitle, articles.articleID, articleText, date, sectionName, articles.sectionID, articles.volumeID, articles.issueNumber, promotion, 
		(SELECT imageID FROM article_images where article_images.articleID = articles.articleID LIMIT 1) as imageID, ';
		
		if ($termsArray===NULL)
			$termsArray = array();
		
		$query .= $this->relevanceQuery($termsArray);
		
		$query .= ' AS relevance FROM articles, sections 
		WHERE articles.sectionID = sections.sectionID '.
		($media=='print' ? ' AND volumeID IS NOT NULL ' : '').($media=='web' ? ' AND volumeID IS NULL ' : '').
		($authorID > 0 ? ' AND '.$authorID.' IN (SELECT authorID from article_authors WHERE article_authors.articleID=articles.articleID) ' : '').
		($sectionID!='' ? ' AND articles.sectionID IN ('.$sectionID.')' : '').
		($volumeID > 0 ? ' AND articles.volumeID = '.$volumeID.' ' : '').
		($issueNumber > 0 ? ' AND articles.issueNumber = '.$issueNumber.' ' : '').
		'GROUP BY articles.articleID '.
		(sizeof($termsArray)==0 || $termsArray[0]=='' ? '' : 'HAVING relevance>0 ').
		'ORDER BY '.($sortby=='sectiondescpromotiondesc' ? ('articles.sectionID, promotion DESC ') :
		(($sortby=='dateasc' ? 'date ASC,' : '').($sortby=='datedesc' ? 'date DESC,' : '') .' relevance DESC, hits DESC'));
		
		return mysqli_query($this->db, $query);
				
	}
	
	protected function queryArticleAuthorsSearch($termsArray, $media, $sectionID, $volumeID, $issueNumber)
	{
		$query = 'SELECT authors.authorID, concat(authorLastName, ", ", authorFirstName) AS authorName, '.$this->relevanceQuery($termsArray).' as relevance 
		FROM articles, article_authors, authors WHERE
		articles.articleID = article_authors.articleID AND article_authors.authorID = authors.authorID'.
		($media=='print' ? ' AND articles.volumeID IS NOT NULL ' : '').($media=='web' ? ' AND volumeID IS NULL ' : '').
		($sectionID!='' ? ' AND articles.sectionID IN ('.$sectionID.')' : '').
		($volumeID > 0 ? ' AND articles.volumeID = '.$volumeID.' ' : '').
		($issueNumber > 0 ? ' AND articles.issueNumber = '.$issueNumber.' ' : '').
		' GROUP BY authors.authorID '.
		(count($termsArray)>0 ? ' HAVING relevance > 0 ' : '')
		.'ORDER BY authorName ASC';
		
		return mysqli_query($this->db, $query);
	}
	
	protected function queryIssuePages($volumeID, $issueNumber)
	{
		$query = 'SELECT pageID, pageNumber FROM pages, issues WHERE issues.volumeID = '.$volumeID.' AND issues.issueNumber = '.$issueNumber.'
		AND issues.volumeID=pages.volumeID AND issues.issueNumber=pages.issueNumber AND issueDate IS NOT NULL ORDER BY pageNumber ASC';
		
		return mysqli_query($this->db, $query);			
	}
	
	protected function insertAuthor($firstName, $lastName)
	{
		$query = 'INSERT INTO authors VALUES (NULL, "'.$firstName.'", "'.$lastName.'")';
		
		mysqli_query($this->db, $query);
	}
	
	protected function queryLastAuthor()
	{
		$query = 'SELECT * FROM authors ORDER BY authorID DESC LIMIT 1';
		
		$results = mysqli_query($this->db, $query);
		
		return mysqli_fetch_assoc($results);
		
	}
	
	protected function insertArticle($sectionid, $volumeID, $issueNumber, $pagenumber, $title, $body, $issueDate, $promoted)
	{			
		$allwords = array($body);
		
		$delimiters = array("<br />", " ", "\n", chr(226), "/");
		//anything used to delimit between words -- "<br />" must come before " " because it includes a SPACE
		
		for ($j=0; $j<count($allwords); $j++)
		{
			for ($k=0; $k<count($delimiters); $k++)
			{
				$explosion = explode($delimiters[$k], $allwords[$j]);
				$allwords[$j]=$explosion[0];
				for ($l=1; $l<count($explosion); $l++)
				{
					$allwords[count($allwords)] = $explosion[$l];
				}
			}
		}
		
		$keywords = array();
		for ($j=0; $j<count($allwords); $j++)
		{
			$parsedWord = Helper::alphaParse($allwords[$j], false);

			if ($parsedWord && strlen($parsedWord)>2 && !IgnoreWordList::isIgnoreWord($parsedWord))
			{
				if (!isset($keywords[$parsedWord]))
					$keywords[$parsedWord] = 1;
				else
					$keywords[$parsedWord]++;
			}
		}
		
		//combine keywords that only differ by one char at the end -- i.e. plurals
		foreach ($keywords as $key => $value)
		{
			if (isset($keywords[substr($key, 0, strlen($key)-1)]))
			{
				$keywords[substr($key, 0, strlen($key)-1)] += $value;
				$keywords[$key] = 0;
			}
		}
		
		$keywordString = ' ';
		foreach ($keywords as $key => $value)
		{	
			$keywordString .= ($value>=4 ? $key.' ' : '');
		}
	
		$articleQuery = 'INSERT INTO articles VALUES (';
		
		$articleQuery .= 'NULL, '.$sectionid.', '.$volumeID.', '.$issueNumber.', '.$pagenumber.', "'.$title.'", "'.$body.'",
		"'.$issueDate.'", "'.trim($keywordString).'", '.$promoted.', 0)';
		
		mysqli_query($this->db, $articleQuery);
	}
	
	protected function queryLastArticle()
	{
		$query = 'SELECT * FROM articles ORDER BY articleID DESC LIMIT 1';
		
		return mysqli_fetch_assoc(mysqli_query($this->db, $query));
	}
	
	protected function insertArticleAuthor($articleID, $authorID)
	{
		$query = 'INSERT INTO article_authors VALUES ('.$articleID.', '.$authorID.')';
		
		mysqli_query($this->db, $query);
	}
	
	protected function insertPage($volumeID, $issueNumber, $pageNumber)
	{
		$query = 'INSERT INTO pages VALUES (NULL, '.$volumeID.', '.$issueNumber.', '.$pageNumber.')';
		
		mysqli_query($this->db, $query);
	}
	
	protected function queryLastPage()
	{
		$query = 'SELECT * FROM pages ORDER BY pageID DESC LIMIT 1';
		
		return mysqli_fetch_assoc(mysqli_query($this->db, $query));
	}
	
	protected function insertImage($caption, $credit)
	{
		$query = 'INSERT INTO images VALUES (NULL, "'.$caption.'", "'.$credit.'")';
		
		mysqli_query($this->db, $query);
	}
	
	protected function queryLastImage()
	{
		$query = 'SELECT * FROM images ORDER BY imageID DESC LIMIT 1';
		
		return mysqli_fetch_assoc(mysqli_query($this->db, $query));
	}
	
	protected function insertArticleImage($articleID, $imageID)
	{
		$query = 'INSERT INTO article_images VALUES ( '.$articleID.', '.$imageID.')';
		
		mysqli_query($this->db, $query);
	}
	
	protected function updateIssue($volumeID, $issueNumber, $issueDate)
	{
		$query = 'UPDATE issues SET issueDate="'.$issueDate.'" WHERE volumeID='.$volumeID.' AND issueNumber='.$issueNumber;
		
		mysqli_query($this->db, $query);
	}
	
	protected function insertIssue($volumeID, $issueNumber, $googleArticleFolderID, $googlePageFolderID, $googleImageFolderID)
	{
		$query = 'INSERT INTO issues VALUES ('.$volumeID.', '.$issueNumber.',
		"'.$googleArticleFolderID.'", "'.$googlePageFolderID.'", "'.$googleImageFolderID.'", NULL)';
		
		mysqli_query($this->db, $query);	
	}

}

?>