Utiliser le script final
------------------------------------
  Syntaxe :
    python3 script.py --haut_tot <m> --haut_tronc <m> --tronc_diam <m> [--clc_nbr_diag <n>] [--remarquable Oui|Non]

  Exemples :
    # Petit arbre
    python3 script.py --haut_tot 4.0 --haut_tronc 1.2 --tronc_diam 0.20

    # Arbre adulte
    python3 script.py --haut_tot 10.0 --haut_tronc 2.8 --tronc_diam 0.80 --clc_nbr_diag 2

    # Grand arbre remarquable
    python3 script.py --haut_tot 22.0 --haut_tronc 5.0 --tronc_diam 1.80 --clc_nbr_diag 2 --remarquable Oui