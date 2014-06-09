\documentclass[a4paper,8pt]{extarticle}
\usepackage{geometry} % paper=a4paper
\usepackage[frenchb]{babel}
\usepackage[utf8]{inputenc}
\usepackage{units}
\usepackage{geometry}
\usepackage{graphicx}
\usepackage{fp}
\usepackage[table]{xcolor}
\usepackage{multicol}
\usepackage{textcomp}
\usepackage{marvosym}
\usepackage{truncate}
\usepackage{tabularx}
\usepackage{multirow}
\usepackage{amssymb}
\usepackage{ulem}
\usepackage{fmtcount}

\pagestyle{empty}

\renewcommand\sfdefault{phv}
\renewcommand{\familydefault}{\sfdefault}
\renewcommand{\TruncateMarker}{\small{...}}

\newcommand{\euro}{\EUR\xspace}

\newcommand{\squareChecked}{\makebox[0pt][l]{$\square$}\raisebox{.15ex}{\hspace{0.1em}$\checkmark$}}

\setlength{\oddsidemargin}{-1cm}
\setlength{\evensidemargin}{-1cm}
\setlength{\textwidth}{18cm}
\setlength{\textheight}{27.9cm}
\setlength{\topmargin}{-3cm}
\setlength{\parindent}{0pt}

<?php include_partial('vrac/generateEnteteTex', array('vrac' => $vrac)); ?>

<?php include_partial('vrac/generateBodyTex', array('vrac' => $vrac)); ?>

							