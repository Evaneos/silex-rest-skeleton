#!/usr/bin/env bash
git remote rm origin
git remote add origin $1
git remote add upstream git@github.com:Evaneos/silex-rest-skeleton.git
