w
ps aux | more
top
ls
cd
ls
lynx http://download.sourceforge.net/blacknova/bnt-0.1.9.tar.gz
tar -tzf bnt-0.1.9.tar.gz 
tar -xvzf bnt-0.1.9.tar.gz 
cd blacknova/
ls
exit
export CVS_RSH=ssh 
cvs -d:pserver:anonymous@cvs.blacknova.sourceforge.net:/cvsroot/blacknova login 
cvs -d:pserver:harwoodr@cvs.blacknova.sourceforge.net:/cvsroot/blacknova login 
cvs -d:ext:harwoodr@cvs.blacknova.sourceforge.net:/cvsroot/blacknova import blacknova vendor start
exit
