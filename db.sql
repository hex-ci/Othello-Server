-- phpMyAdmin SQL Dump
-- version 4.4.10
-- http://www.phpmyadmin.net

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `othello`
--

-- --------------------------------------------------------

--
-- 表的结构 `othello_chat`
--

CREATE TABLE IF NOT EXISTS `othello_chat` (
  `id` int(11) NOT NULL,
  `Name` varchar(15) NOT NULL,
  `ChatText` varchar(100) NOT NULL,
  `ChatDate` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

-- --------------------------------------------------------

--
-- 表的结构 `othello_online`
--

CREATE TABLE IF NOT EXISTS `othello_online` (
  `id` int(11) NOT NULL,
  `UserName` varchar(15) NOT NULL,
  `Name` varchar(15) DEFAULT NULL,
  `IP` varchar(15) NOT NULL,
  `LANIP` varchar(15) DEFAULT NULL,
  `Port` int(11) NOT NULL DEFAULT '0',
  `LastTime` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

-- --------------------------------------------------------

--
-- 表的结构 `othello_security`
--

CREATE TABLE IF NOT EXISTS `othello_security` (
  `id` int(11) NOT NULL,
  `Security1` varchar(20) NOT NULL,
  `Security2` varchar(20) NOT NULL,
  `MakeDate` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

-- --------------------------------------------------------

--
-- 表的结构 `othello_table`
--

CREATE TABLE IF NOT EXISTS `othello_table` (
  `id` int(11) NOT NULL,
  `TableName` varchar(15) NOT NULL,
  `Creator` varchar(15) NOT NULL,
  `CreatorName` varchar(15) DEFAULT NULL,
  `Visitor` varchar(15) DEFAULT NULL,
  `VisitorName` varchar(15) DEFAULT NULL,
  `Type` int(4) NOT NULL DEFAULT '0',
  `GameTimer` int(4) NOT NULL DEFAULT '0',
  `Level` int(4) NOT NULL DEFAULT '0',
  `IP` varchar(15) NOT NULL,
  `LANIP` varchar(15) DEFAULT NULL,
  `Port` int(11) NOT NULL DEFAULT '0',
  `LastTime` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

-- --------------------------------------------------------

--
-- 表的结构 `othello_user`
--

CREATE TABLE IF NOT EXISTS `othello_user` (
  `id` int(11) NOT NULL,
  `UserName` varchar(15) NOT NULL,
  `Password` varchar(32) NOT NULL,
  `Email` varchar(30) NOT NULL,
  `RegisterDate` datetime NOT NULL,
  `LastLogin` datetime NOT NULL,
  `UserClass` varchar(2) NOT NULL,
  `Face` int(4) NOT NULL DEFAULT '0',
  `Name` varchar(15) DEFAULT NULL,
  `Sex` int(4) NOT NULL DEFAULT '0',
  `Age` int(4) NOT NULL DEFAULT '0',
  `Country` varchar(20) DEFAULT NULL,
  `State` varchar(20) DEFAULT NULL,
  `City` varchar(20) DEFAULT NULL,
  `Win` int(11) NOT NULL DEFAULT '0',
  `Lose` int(11) NOT NULL DEFAULT '0',
  `Draw` int(11) NOT NULL DEFAULT '0',
  `Score` int(11) NOT NULL DEFAULT '0',
  `GameTimes` int(11) NOT NULL DEFAULT '0',
  `DisconnectTimes` int(11) NOT NULL DEFAULT '0',
  `Security1` varchar(20) NOT NULL,
  `Security2` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `othello_chat`
--
ALTER TABLE `othello_chat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ChatDate` (`ChatDate`);

--
-- Indexes for table `othello_online`
--
ALTER TABLE `othello_online`
  ADD PRIMARY KEY (`id`),
  ADD KEY `UserName` (`UserName`);

--
-- Indexes for table `othello_security`
--
ALTER TABLE `othello_security`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `othello_table`
--
ALTER TABLE `othello_table`
  ADD PRIMARY KEY (`id`),
  ADD KEY `TableName` (`TableName`);

--
-- Indexes for table `othello_user`
--
ALTER TABLE `othello_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `UserName` (`UserName`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `othello_chat`
--
ALTER TABLE `othello_chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `othello_online`
--
ALTER TABLE `othello_online`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `othello_security`
--
ALTER TABLE `othello_security`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `othello_table`
--
ALTER TABLE `othello_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `othello_user`
--
ALTER TABLE `othello_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
