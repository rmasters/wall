<?php

class Browser extends Model
{
    protected $id;
    protected $post;
    protected $userAgent;
    protected $address;
    
    public function setId($id) {
        $this->id = (int) $id;
    }
    
    public function setPost($id) {
        $this->post = (int) $id;
    }
    
    public function save() {
        $db = self::getDb();
        
        $values = array(
            "id" => $this->getId(),
            "post" => $this->getPost(),
            "user-agent" => $this->getUserAgent(),
            
        );
        
        if ($this->id) {
            $sql = "UPDATE browsers
                SET post = :post,
                    user-agent = :userAgent,
                    address = :address
                WHERE id = :id
                LIMIT 1";
        } else {
            $sql = "INSERT INTO browsers
                (post, user-agent, address) VALUES
                (:post, :userAgent, :address)";
                
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
        
        $stmt = $db->prepare("SELECT * FROM browsers WHERE id = :id LIMIT 1");
        $stmt->execute(array(":id" => $id));
        $model->setOptions($stmt->fetch(PDO::FETCH_ASSOC));
        $stmt->closeCursor();
        
        return $model;
    }
    
    public static function fetchAll() {
        $models = array();
        $db = self::getDb();
        
        $stmt = $db->prepare("SELECT * FROM browsers");
        $stmt->execute();
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $models[] = new self($row);
        }
        $stmt->closeCursor();
        
        return $models;
    }
    
    public function delete() {
        $db = self::getDb();
        
        $delete = $db->prepare("DELETE FROM browsers WHERE id = :id LIMIT 1");
        $delete->execute(array(":id" => $this->id));
        
        return null;
    }
    
    public static function create() {
        $db = self::getDb();
        
        $create = "CREATE TABLE browsers (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            post INTEGER,
            user-agent TEXT,
            address TEXT
        )";
        $db->exec($create);
    }
    
    public static function truncate() {
        $db = self::getDb();
        
        $truncate = "DELETE FROM browsers";
        $db->exec($truncate);
    }
    
    public static function drop() {
        $db = self::getDb();
        
        $drop = "DROP TABLE browsers";
        $db->exec($drop);
    }
}
