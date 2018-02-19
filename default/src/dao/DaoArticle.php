<?php

namespace simplon\dao;
use simplon\entities\User;
use simplon\entities\Article;
use simplon\dao\Connect;
/**
 * Un Dao, pour Data Access Object, est une classe dont le but est de faire
 * le lien entre les tables SQL et les objets PHP (ou autre langage).
 * Le but est de centraliser dans la ou les classes DAO tous les appels
 * SQL pour ne pas avoir de SQL qui se balade partout dans note application
 * (comme ça, si on change de SGBD, ou de table, ou de database, on aura
 * juste le DAO à modifier et le reste de notre appli restera inchangé)
 */
class DaoArticle {
    
    
    /**
     * La méthode getAll renvoie toutes les Articles stockées en bdd
     * @return Article[] la liste des Article ou une liste vide
     */
    public function getAll():array {
        $tab = [];
        
        try {
            $query = Connect::getInstance()->prepare('SELECT * FROM article');
            $query->execute();
            while($row = $query->fetch()) {
                $art = new Article($row['title'], 
                            $row['content'], 
                            new \DateTime($row['date']),
                            $row['id']);
                //On ajoute la Article créée à notre tableau
                $tab[] = $art;
            }
        }catch(\PDOException $e) {
            echo $e;
        }
        //On return le tableau
        return $tab;
    }

    public function getUserArticle($id):array {
        $tab = [];
        
        try {
            $query = Connect::getInstance()->prepare('SELECT * FROM article INNER JOIN user ON user.id = article.user_id WHERE user.id = :id');
            $query->bindValue(':id', $id, \PDO::PARAM_INT);
            $query->execute();
            while($row = $query->fetch()) {
                $art = new Article($row['title'],$row['user_id'], 
                            $row['content'], 
                            new \DateTime($row['date']),
                            $row['id']);
                //On ajoute la Article créée à notre tableau
                $tab[] = $art;
            }
        }catch(\PDOException $e) {
            echo $e;
        }
        //On return le tableau
        return $tab;
    }
    /**
     * Méthode permettant de récupérer une Article en se basant sur
     * son Id
     * @return Article|null renvoie soit la Article correspondante soit null
     * si pas de match
     */
    public function getById(int $id) {
        
        try {
            $query = Connect::getInstance()->prepare('SELECT * FROM article WHERE id=:id');
           
            $query->bindValue(':id', $id, \PDO::PARAM_INT);
            $query->execute();
            if($row = $query->fetch()) {
                //On crée une instance de Article
                $art = new Article($row['title'], 
                            $row['content'], 
                            new \DateTime($row['date']),
                            $row['id']);
                //On return cette Article
                return $art;
            }
        }catch(\PDOException $e) {
            echo $e;
        }
    
        return null;
    }
    /**
     * Méthode permettant de faire persister en base de données une 
     * instance de Article passée en argument.
     */
    public function add(Article $art) {
        
        try {
            //On prépare notre requête, avec les divers placeholders
            $query = Connect::getInstance()->prepare('INSERT INTO article (user_id,title,content,date) VALUES (:user_id, :title, :content, :date)');
           
            $query->bindValue(':title',$art->getTitle(),\PDO::PARAM_STR);
            $query->bindValue(':user_id',$art->getUserId(),\PDO::PARAM_INT);
            $query->bindValue(':content',$art->getContent(),\PDO::PARAM_STR);
            $query->bindValue(':date',$art->getDate()->format('Y-m-j H:i:s'),\PDO::PARAM_STR);

            $query->execute();
            /**
             * On fait en sorte de récupérer le dernier id généré par SQL 
             * afin de l'assigner à l'id de notre instance de Article
             */
            $art->setId(Connect::getInstance()->lastInsertId());
            
        }catch(\PDOException $e) {
            echo $e;
        }
    }
    /**
     * Une méthode pour mettre à jour les informations d'une Article 
     * déjà existante dans la base de donnée.
     * L'argument $art doit être une instance de Article complète, avec
     * un id existant en base.
     */
    public function update(Article $art) {
        
        try {
            //toujours pareil, on prépare la requête
            $query = Connect::getInstance()->prepare('UPDATE article SET title = :title, content = :content, date = :date WHERE id = :id');
            //on bind les value des placeholders
            $query->bindValue(':title',$art->getTitle(),\PDO::PARAM_STR);
            
            $query->bindValue(':content',$art->getContent(),\PDO::PARAM_STR);
            $query->bindValue(':id',$art->getId(),
            \PDO::PARAM_INT);
            $query->execute();
            
            
        }catch(\PDOException $e) {
            echo $e;
        }
    }

    public function delete(int $id) {
        
        try {
            //On prépare...
            $query = Connect::getInstance()->prepare('DELETE FROM article WHERE id = :id');
            //on bind...
            $query->bindValue(':id',$id,\PDO::PARAM_INT);
            $query->execute();
            
            
        }catch(\PDOException $e) {
            echo $e;
        }
    }


}