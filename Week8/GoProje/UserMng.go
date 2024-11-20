package main

import (
	"fmt"
	"log"
	"os"
)

var credentials = map[string]string{
	"Admin": "admin",
	"User":  "user",
}

func main() {
	file, _ := os.Create("Systm.log")
	log.SetOutput(file)

	var action int

loop:
	for true {
		fmt.Print("Komut Girin: ")
		fmt.Scan(&action)

		switch action {
		default:
			fmt.Println("Geçersiz Komut\n" +
				"----------------------")
			fmt.Println("-------Komutlar-------\n" +
				"Kullanıcı Girişi --> 0\n" +
				"Admin Girişi ------> 1\n" +
				"Çıkış -------------> 9")

		case 0:
			var username, passwd string
			fmt.Println("Kullanıcı Girişi")
			fmt.Print("Kullanıcı Adı Girin: ")
			fmt.Scan(&username)
			fmt.Print("Şifre Girin: ")
			fmt.Scan(&passwd)

			ok := userLogin(username, passwd)
		userLoop:
			for ok {
				fmt.Println("Hoşgeldin ", username)
				var userAction int
				fmt.Print("Komut Girin: ")
				fmt.Scan(&userAction)

				switch userAction {
				default:
					fmt.Println("Geçersiz Komut\n" +
						"----------------------")
					fmt.Println("-------Komutlar-------\n" +
						"Şifre Değiştir ----> 0\n" +
						"Çıkış -------------> 9")
				case 0:
					var oldPasswd, newPasswd, checkPasswd string

					fmt.Print("Eski Parolanı Gir: ")
					fmt.Scan(&oldPasswd)
					fmt.Print("Yeni Parola Gir: ")
					fmt.Scan(&newPasswd)
					fmt.Print("Yeni Parolayı Tekrar Gir: ")
					fmt.Scan(&checkPasswd)

					if oldPasswd != credentials[username] {
						fmt.Println("Parolanı Yanlış Girdin.")
					} else if newPasswd != checkPasswd {
						fmt.Println("Yeni parolan birbili ile uyuşmadı.")
					} else if newPasswd == credentials[username] {
						fmt.Println("Yeni parolan eskisi ile aynı olamaz.")
					} else {
						credentials[username] = newPasswd
						log.Println(username, "Kullanıcısının şifresi değiştirildi")
						fmt.Println("Parolan değiştirildi tekrar giriş yap!")
						break userLoop
					}

				case 9:
					fmt.Println(username, " Kullanıcısından Çıkış Yapılıyor....")
					break userLoop
				}
			}

		case 1:
			var username, passwd string
			fmt.Println("Kullanıcı Girişi")
			fmt.Print("Kullanıcı Adı Girin: ")
			fmt.Scan(&username)
			fmt.Print("Şifre Girin: ")
			fmt.Scan(&passwd)

			ok := adminLogin(username, passwd)
		adminLoop:
			for ok {
				fmt.Println("Hoşgeldin ", username)
				var adminAction int
				fmt.Print("Komut Girin: ")
				fmt.Scan(&adminAction)

				switch adminAction {
				default:
					fmt.Println("Geçersiz Komut\n" +
						"----------------------")
					fmt.Println("-------Komutlar-------\n" +
						"Kullanıcı Liste----> 0\n" +
						"Kullanıcı Ekle ----> 1\n" +
						"Kullanıcı Sil -----> 2\n" +
						"Log Kayıdı --------> 3\n" +
						"Çıkış -------------> 9")
				case 0:
					fmt.Println("Kayıtlı Kullanıcılar:")
					for username := range credentials {
						fmt.Println(username)
					}
				case 1:
					var newUsername, newPasswd string

					fmt.Print("Kullanıcı Adı Gir: ")
					fmt.Scan(&newUsername)
					fmt.Print("Parola Gir: ")
					fmt.Scan(&newPasswd)

					addUser(newUsername, newPasswd)
				case 2:
					var delUsername string

					fmt.Print("Kullanıcı Adı Gir: ")
					fmt.Scan(&delUsername)

					deleteUser(delUsername)
				case 3:
					listLog()
				case 9:
					log.Println(username, "Kullanıcısı çıkış yaptı")
					fmt.Println(username, " Kullanıcısından Çıkış Yapılıyor....")
					break adminLoop
				}
			}

		case 9:
			fmt.Println("Çıkış Yapılıyor....")
			file.Close()
			break loop
		}

	}
}

func userLogin(username, passwd string) bool {
	password, ok := credentials[username]

	if username == "Admin" || username == "admin" {
		log.Println(username, "Kullanıcısı Admin girişi yapmaya çalıştı")
		fmt.Println("Yetkisiz Giriş Denemesi....")
	} else if ok && passwd == password {
		log.Println(username, "Kullanıcısı giriş yaptı")
		fmt.Println("Başarılı Giriş")
		return true
	}

	log.Println(username, "Kullanıcısı hatalı giriş yaptı")
	fmt.Println("Geçersiz Kullanıcı Adı yada Parola")
	return false
}

func adminLogin(username, passwd string) bool {
	password, ok := credentials[username]

	if ok && passwd == password && username == "Admin" {
		log.Println(username, "Kullanıcısı giriş yaptı")
		fmt.Println("Başarılı Giriş")
		return true
	}

	log.Println(username, "Kullanıcısı hatalı giriş yaptı")
	fmt.Println("Geçersiz Kullanıcı Adı yada Parola")
	return false
}

func addUser(username, passwd string) {
	log.Println(username, "Kullanıcısı eklendi")
	credentials[username] = passwd
}

func deleteUser(username string) {
	log.Println(username, "Kullanıcısı silindi")
	delete(credentials, username)
}

func listLog() {
	dat, _ := os.ReadFile("Systm.log")
	fmt.Print(string(dat))
}
