// Written By: Adam Voigt <avoigt@users.sourceforge.net>
// This is an early version, it's not fancy but it gets the job done.
// I will revise when I get some more time.

#include <fstream.h>

fstream fileout("config_local.php3",ios::out);

void intro();
void general(char gameroot[100], char link_forums[100], char webpath[100]);
void database(char dbhost[100], char dbport[10], char dbusername[100], char dbpassword[100], char dbname[100]);
void admin(char admin_pass[100], char admin_email[100]);
void writefile(char gameroot[100], char link_forums[100], char dbhost[100], char dbport[10], char dbusername[100], char dbpassword[100], char dbname[100], char admin_pass[100], char admin_email[100]);
void cronfile(char cronpath[100], char admin_pass[100], char gameroot[100], char lynxpath[100], char touchpath[100], char webpath[100]);
void close();

int main()
{
	char gameroot[100];
	char webpath[100] = "http://localhost/blacknova/";
	char link_forums[100] = "http://blacknova.community.everyone.net/commun_v3/scripts/directory.pl";
	char cronpath[100] = "/etc/crontab";
	char lynxpath[100] = "/usr/bin/lynx";
	char touchpath[100] = "/bin/touch";
	
	char dbhost[100] = "localhost";
	char dbport[10] = "3306";
	char dbusername[100];
	char dbpassword[100];
	char dbname[100] = "blacknova";
	
	char admin_pass[100];
	char admin_email[100];
	
	intro();
	general(gameroot, link_forums, webpath);
	database(dbhost, dbport, dbusername, dbpassword, dbname);
	admin(admin_pass, admin_email);
	writefile(gameroot, link_forums, dbhost, dbport, dbusername, dbpassword, dbname, admin_pass, admin_email);
	cronfile(cronpath, admin_pass, gameroot, lynxpath, touchpath, webpath);
	close();
	
	return(0);
}

void intro()
{
	cout << endl;
	cout << "***********************************************" << endl;
	cout << "**** Blacknova Installer Version 1.0       ****" << endl;
	cout << "**** Installer By: Adam Voigt              ****" << endl;
	cout << "**** http://www.sourceforge.net/blacknova/ ****" << endl;
	cout << "***********************************************" << endl;
	cout << endl;
}

void general(char gameroot[100], char link_forums[100], char webpath[100])
{
	char temp1[100];
	char temp2[100];
	cout << "Enter the root directory on the filesystem" << endl;
	cout << "where BlackNova will be installed. Do not" << endl;
	cout << "enter a / after the path." << endl << endl;
	cout << "-> ";
	cin.getline(gameroot,100);
	cout << endl << endl;
	
	cout << "Enter the path you would type in your web browser" << endl;
	cout << "to get to blacknova from the machine it's hosted on." << endl;
	cout << "Such as: http://localhost/blacknova/ or enter: -" << endl;
	cout << "to use this path." << endl << endl;
	cout << "-> ";
	cin.getline(temp2,100);
	cout << endl << endl;
	if(temp2[0] != '-')
		strcpy(webpath,temp2);
	
	cout << "Enter the link you wish to use for the" << endl;
	cout << "forums link. If you wish to use the default" << endl;
	cout << "BlackNova forums, enter: -" << endl;
	cout << endl;
	cout << "-> ";
	cin.getline(temp1,100);
	
	if(temp1[0] != '-')
		strcpy(link_forums,temp1);

	cout << endl << endl;
}

void database(char dbhost[100], char dbport[10], char dbusername[100], char dbpassword[100], char dbname[100])
{
	char temp1[100];
	char temp2[10];
	char temp3[100];
	cout << "Enter the hostname or IP address of the" << endl;
	cout << "database. If it is on this machine," << endl;
	cout << "enter: - for localhost" << endl << endl;
	cout << "-> ";
	cin.getline(temp1,100);
	
	if(temp1[0] != '-')
		strcpy(dbhost,temp1);
	
	cout << endl << endl;
	cout << "Enter the port you wish to connect" << endl;
	cout << "to that hosts the database. The default" << endl;
	cout << "MySQL port is 3306, to use the default enter: -" << endl << endl;
	cout << "-> ";
	cin.getline(temp2,10);
	
	if(temp2[0] != '-')
		strcpy(dbport,temp2);
	
	cout << endl << endl;
	cout << "Enter the username you wish" << endl;
	cout << "to use to connect to the database." << endl << endl;
	cout << "-> ";
	cin.getline(dbusername,100);
	cout << endl << endl;
	cout << "Enter the password you wish to use to" << endl;
	cout << "connect to the database." << endl << endl;
	cout << "-> ";
	cin.getline(dbpassword,100);
	cout << endl << endl;
	cout << "Enter the MySQL database name to" << endl;
	cout << "use to run BlackNova. To use the" << endl;
	cout << "default which is blacknova, enter: -" << endl << endl;
	cout << "-> ";
	cin.getline(temp3,100);
	
	if(temp3[0] != '-')
		strcpy(dbname,temp3);
	
	cout << endl << endl;
}

void admin(char admin_pass[100], char admin_email[100])
{
	cout << "Enter the Administrator's email address." << endl << endl;
	cout << "-> ";
	cin.getline(admin_email,100);
	cout << endl << endl;
	cout << "Enter the Administrator's password." << endl << endl;
	cout << "-> ";
	cin.getline(admin_pass,100);
	cout << endl << endl;
}

void writefile(char gameroot[100], char link_forums[100], char dbhost[100], char dbport[10], char dbusername[100], char dbpassword[100], char dbname[100], char admin_pass[100], char admin_email[100])
{
	fileout << "$gameroot = \"" << gameroot << "\";" << endl;
	fileout << "$dbhost = \"" << dbhost << "\";" << endl;
	fileout << "$dbport = \"" << dbport << "\";" << endl;
	fileout << "$dbuname = \"" << dbusername << "\";" << endl;
	fileout << "$dbpass = \"" << dbpassword << "\";" << endl;
	fileout << "$dbname = \"" << dbname << "\";" << endl;
	fileout << "$adminpass = \"" << admin_pass << "\";" << endl;
	fileout << "$admin_email = \"" << admin_email << "\";" << endl;
	fileout << "$link_forums = \"" << link_forums << "\";";
}

void close()
{
	cout << "Config File Successfully Generated." << endl;
	cout << "Thank you for trying BlackNova." << endl;
}

void cronfile(char cronpath[100], char admin_pass[100], char gameroot[100], char lynxpath[100], char touchpath[100], char webpath[100])
{
	char temp1[100];
	char temp2[100];
	char temp3[100];
	cout << "BlackNova requires a crontab entry" << endl;
	cout << "to perform required updates." << endl;
	cout << "The installer can make this change for you," << endl;
	cout << "enter the path to your crontab file below," << endl;
	cout << "enter a - for the default /etc/crontab, or enter" << endl;
	cout << "a ! to add the entry manually and leave the crontab untouched." << endl << endl;
	cout << "-> ";
	cin.getline(temp1,100);
	cout << endl << endl;
	
	if(temp1[0] != '!')
	{
		if(temp1[0] != '-')
			strcpy(cronpath,temp1);
		
		cout << "BlackNova requires lynx to automatically" << endl;
		cout << "run updates. Please enter the path to lynx" << endl;
		cout << "on your system, or enter: -" << endl;
		cout << "to use the default which is /usr/bin/lynx" << endl << endl;
		cout << "-> ";
		cin.getline(temp2,100);
		cout << endl << endl;
		
		if(temp2[0] != '-')
			strcpy(lynxpath,temp2);
		
		cout << "BlackNova requires the touch program to" << endl;
		cout << "update file modification times. Please enter" << endl;
		cout << "the path to touch, or enter: -" << endl;
		cout << "to use the default path of /bin/touch" << endl << endl;
		cout << "-> ";
		cin.getline(temp3,100);
		cout << endl << endl;
		
		if(temp3[0] != '-')
			strcpy(touchpath,temp3);
		
		fstream cronfile(cronpath,ios::app);

		cronfile << "*/6 * * * * " << lynxpath << " --dump " << webpath << "sysupdate.php3?swordfish=" << admin_pass << " > /dev/null" << endl;
		cronfile << "*/15 * * * * /usr/bin/lynx --source " << webpath << "genrank.php3" << " > " << gameroot << "/ranking.php3" << endl;
		cronfile << "*/6 * * * * " << touchpath << " " << gameroot << "/cron.txt" << endl;
	}
}