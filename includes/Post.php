<?php

class Post extends Model
{
    protected $id;
    protected $name;
    protected $content;
    protected $posted;
    protected $hidden = false;
    protected $preformatted = false;
    
    public function getId() {
        return $this->id;
    }
    
    public function setId($id) {
        $this->id = (int) $id;
    }
    
    public function getName() {
        if ($this->name == null) {
            return "Anonymous";
        }
        return $this->name;
    }
    
    public function setName($name) {
        $name = self::protect($name);
        
        if (!self::valid($name)) {
            $name = "Anonymous";
        }
        
        if (strlen($name) > 50) {
            $name = substr($name, 0, 47) . "...";
        }
        $this->name = $name;
    }
    
    public function getContent() {
        return self::protect($this->content);
    }
    
    public function getContentRaw() {
        return $this->content;
    }
    
    public function setContent($content) {
        if (!self::valid($content)) {
            throw new UserException("Content was not valid (i.e. it was empty).");
        }
        
        if (strlen($content) > 500) {
            $content = substr(content, 0, 497) . "...";
        }
        $this->content = $content;
    }
    
    public function getPosted() {
        if ($this->posted == null) {
            $this->posted = time();
        }
        return $this->posted;
    }
    
    public function setPosted($posted) {
        if (is_string($posted)) {
            $posted = strtotime($posted);
        }
        $this->posted = (int) $posted;
    }
    
    public function getHidden() {
        return $this->hidden;
    }
    
    public function setHidden($hidden) {
        $this->hidden = (bool) $hidden;
    }
    
    public function getPreformatted() {
        return $this->preformatted;
    }
    
    public function setPreformatted($preformatted) {
        $this->preformatted = (bool) $preformatted;
    }
    
    // Protect from XSS, etc.
    protected static function protect($input) {
        $input = htmlentities($input);
        return $input;
    }
    
    protected static function valid($input) {
        $input = trim($input);
        if (empty($input)) {
            return false;
        }
        
        if (strlen($input) > 500) {
            return false;
        }
        return true;
    }
    
    public function save() {
        $db = self::getDb();
        
        $values = array(
            "id" => $this->getId(),
            "name" => $this->getName(),
            "content" => $this->getContentRaw(),
           "posted" => date("Y-m-d H:i:s", $this->getPosted()),
            "hidden" => (int) $this->getHidden(),
            "preformatted" => (int) $this->getPreformatted()
        );
        
        if ($this->id) {
            $sql = "UPDATE posts
                SET name = :name,
                    content = :content,
                    posted = :posted,
                    hidden = :hidden,
                    preformatted = :preformatted
                WHERE id = :id
                LIMIT 1";
        } else {
            $sql = "INSERT INTO posts
                (name, content, posted, hidden, preformatted) VALUES
                (:name, :content, :posted, :hidden, :preformatted)";
                
            unset($values["id"]);
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($values);
        
        if ($this->id == null) {
            $this->id = $db->lastInsertId();
        }
        
        $stmt->closeCursor();
    }
    
    public static function fetch($id) {
        if ((int) $id < 1) {
            return false;
        }
    
        $model = new self;
        $db = self::getDb();
        
        $stmt = $db->prepare("SELECT * FROM posts WHERE id = :id LIMIT 1");
        $stmt->execute(array(":id" => $id));
        $model->setOptions($stmt->fetch(PDO::FETCH_ASSOC));
        $stmt->closeCursor();
        
        return $model;
    }
    
    public static function fetchAll() {
        $models = array();
        $db = self::getDb();
        
        $stmt = $db->prepare("SELECT * FROM posts WHERE hidden = 0 ORDER BY posted DESC");
        $stmt->execute();
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $models[] = new self($row);
        }
        $stmt->closeCursor();
        
        return $models;
    }
    
    public static function fetchAllWithin($offset, $count) {
        $models = array();
        $db = self::getDb();
        
        $stmt = $db->prepare("SELECT * FROM posts WHERE hidden = 0 ORDER BY posted DESC LIMIT :count OFFSET :offset");
        $stmt->execute(array("offset" => $offset, "count" => $count));
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $models[] = new self($row);
        }
        $stmt->closeCursor();
        
        return $models;
    }
    
    public function delete() {
        $db = self::getDb();
        
        $delete = $db->prepare("DELETE FROM posts WHERE id = :id LIMIT 1");
        $delete->execute(array(":id" => $this->id));
        
        return null;
    }
    
    public static function create() {
        $db = self::getDb();
        
        $create = "CREATE TABLE posts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT,
            content TEXT,
            posted DATETIME,
            hidden INTEGER,
            preformatted INTEGER
        )";
        $db->exec($create);
    }
    
    public static function truncate() {
        $db = self::getDb();
        
        $truncate = "DELETE FROM posts";
        $db->exec($truncate);
    }
    
    public static function drop() {
        $db = self::getDb();
        
        $drop = "DROP TABLE posts";
        $db->exec($drop);
    }
}
