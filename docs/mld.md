# MLD - Touche pas au klaxon

Notation : `PK` = clé primaire, `#` = clé étrangère (FK).

```
agence (
    id_agence     PK  INT UNSIGNED AUTO_INCREMENT,
    nom               VARCHAR(100) NOT NULL UNIQUE
)

employe (
    id_employe    PK  INT UNSIGNED AUTO_INCREMENT,
    nom               VARCHAR(100) NOT NULL,
    prenom            VARCHAR(100) NOT NULL,
    telephone         VARCHAR(20)  NOT NULL,
    email             VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe      VARCHAR(255) NOT NULL,      -- hash password_hash()
    role              ENUM('user','admin') NOT NULL DEFAULT 'user'
)

trajet (
    id_trajet         PK  INT UNSIGNED AUTO_INCREMENT,
    #id_agence_depart     INT UNSIGNED NOT NULL  -> agence.id_agence,
    #id_agence_arrivee    INT UNSIGNED NOT NULL  -> agence.id_agence,
    date_depart           DATETIME NOT NULL,
    date_arrivee          DATETIME NOT NULL,
    places_total          TINYINT UNSIGNED NOT NULL,
    places_dispo          TINYINT UNSIGNED NOT NULL,
    #id_employe           INT UNSIGNED NOT NULL  -> employe.id_employe
)
```

## Contraintes d'intégrité

| Contrainte | Règle |
|---|---|
| `uq_agence_nom` | Deux agences ne peuvent pas porter le même nom |
| `uq_employe_email` | L'adresse email identifie un employé de façon unique |
| `fk_trajet_depart`, `fk_trajet_arrivee` | Un trajet référence deux agences existantes (suppression d'une agence utilisée refusée : RESTRICT) |
| `fk_trajet_employe` | Un trajet a un auteur existant |
| `ck_trajet_agences` | `id_agence_depart <> id_agence_arrivee` |
| `ck_trajet_dates` | `date_arrivee > date_depart` |
| `ck_trajet_places` | `places_total >= 1` et `places_dispo <= places_total` |

## Passage du MCD au MLD

- **PROPOSER** (EMPLOYE 0,N - TRAJET 1,1) : le côté 1,1 reçoit la clé étrangère, donc `trajet.id_employe`.
- **PARTIR_DE** (TRAJET 1,1 - AGENCE 0,N) : `trajet.id_agence_depart`.
- **ARRIVER_A** (TRAJET 1,1 - AGENCE 0,N) : `trajet.id_agence_arrivee`.
- Aucune table d'association n'est nécessaire : aucune cardinalité maximale N des deux côtés.
