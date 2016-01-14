Silex REST Skeleton
===================

You can create a new project using this skeleton by running the following command:
**$>**`composer create-project evaneos/silex-rest-skeleton <your-project-name> -s dev`

At the end, when asked, do not delete the project git files, you might need it to update your skeleton if you're interested in getting updates.
If you don't want to get updates, you can delete it and ignore the **Setup git** part and to as you want.

After that, you'll have to:
1. Setup git
------------
- In your project, set the origin repository; **$>**`git remote set-url origin git@github.com:<vendor-name>/<project-name>`
- Still in your project root, launch **$>**`git pull origin master` and resolve conflicts if you have any
- Then set your upstream by typing this: **$>**`git branch --set-upstream-to origin/master`
- After that, if you want to update from your repository, just do as usual, and if you want to update the skeleton, just type **$>**`git pull composer master` (you can replace `master` by a tag/branch/etc.)


2. Setup the env for docker
---------------------------
- In `infrastructure/environment/dev/php/config`, create your `blackfire-agent.env` file from `blackfire-agent.env.tpl` to set your blackfire conf.
- In `infrastructure/environment/dev/tunnel`, create your `db.env` file from `db.env.tpl` to set your ssh tunnel to the DB machine.
- Build the docker images:
    - Go to `infrastructure/dockerfiles` directory
    - Type the following command **$>**`make build`
- Run your docker containers:
    - Go to `infrastructure/environment\dev` directory
    - Type the following command **$>**`docker-compose up -d`
- To kill your docker containers:
    - Go to `infrastructure/environment\dev` directory
    - Type the following command **$>**`docker-compose kill`
    - Type the following command **$>**`docker-compose rm -f`
    
3. Setup and run your app
-------------------------
- In `config`, create your `config.yml` file to setup your app
- To access your app via http (if your docker containers are running):
    - Type the following command **$>**`docker ps | grep rest-api-nginx` and look for the port in the filed looking like this: `0.0.0.0:<your-port>->80tcp`
    - Your app is now accessible at `http://<your-machine-ip>:<your-port>/`
    
4. Start coding
---------------
- Put your domain code in `src`
- All the app code will go in `app`
    - Your new Controllers will go in `app/API/Controllers` and will be declared in a ServiceProvider
    - ControllerProviders will go in `app/API/ControllerProviders` and will be declared in a ServiceProvider
    - Your routes will be mounted through ControllerProviders in `app/Application::mountRoutes`
    - Your API resources will go in `app/API/Resources`
    - Your parameters converters will go in `app/API/Converters` and will be delcared in `app/ServiceProviders/RestAPIServiceProvider` or in a new ServiceProvider
    - Your ServiceProviders will go in `app/ServiceProviders`
    - Your Domain services will be declared in a new ServiceProvider and registered in `app/Application::addDomainServices` or be registered there directly

5. Console
----------
At the directory root, you'll find a file named `console` which will let you launch commands

To add a command:
- Create it in `app/Commands`
- Add its declaration in `app/ServiceProviders/CommandsServiceProvider` or add a new one
- Add the command to the application in `app/Application::bootCLI`