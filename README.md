# Cardinal Engine(Core) [B]

��� ����� ��� ������� ����� �� ���� ������?

��-������:

 * ������������� ���� config.default.php �� config.php � ����� core/media/
 * ��������� config.php
 * � ��� �� ����� ������������� ���� db.default.php � db.php
 * ��������� db.php
 
����� ��� ������� ����� ����������� � ��������� ��� ������ ��������������� Cardinal Engine. ������ ���� �� ������� ������������� - �� ������� ����� � �� ���������� ����������� *����* �������������� ��� ��������� ����������� ���������� �����������.
 
 * ##��� �������� ������ � ����� ������.
#### ������� ��������� ������(���� ������):
```php
$row = db::doquery("SELECT * FROM news WHERE id = ".intval($_GET['id']));
```
#### ������� ��������� ������(��� ������ � �������):
```php
$rows = db::doquery("SELECT * FROM news ORDER BY id DESC", true);
while($row = db::fetch_assoc()) {
...
}
```

* ##��� �������� ������ � ��������������.
#### ���������� ������ � ������������(���� ������):
```php
templates::assing_var("is_view", "1");
```
#### ���������� ������ � ������������(��������� �������):
```php
templates::assing_vars(array(
"is_view1" => "1",
"is_view2" => "2",
));
```
#### ���������� ������ � ������������(��������� ������� ������):
```php
for($i=0;$i<10;$i++) {
  templates::assing_vars(array(
  "is_view1" => "1",
  "is_view2" => "2",
  ), "news", "news".$i);
}
```

* ##��� ��������, ��������������� ������ � ��������.
#### �������:
```php
[if 1==1]true[/if]
```
```php
[if 1==2]true[else]false[/if]
```
```php
[if 1==1]true[/if 1==1]
```
```php
[if 1==2]true[else 1==2]false[/if 1==2]
```
������� ����� ���� �������� ������ ��� ������� �� ������� ����������
```php
[if 1==2]
	true
[else 1==2]
	[if 1==1]true[else 1==1]false[/if 1==1]
[/if 1==2]
```

* #### �����:
```php
[foreach block=news]
 <h1>{news.is_view1}</h1>
 {news.is_view2}
[/foreach]
```
```php
[foreach block=news]
 <h1>{news.is_view1}</h1>
 {news.is_view2}
[/foreach news]
```
##### !��������! ������ ����� ����������� �������� �� ������� ������� ����������� �� ������ �������:
```php
[foreach block=news]
[foreachif {news.is_view1}==1]
 <h1>{news.is_view1}</h1>[/foreachif]
 {news.is_view2}
[/foreach]
```

#���� � ��� ���� �����-�� ������� - ���������� �� ��-������: [email]
� ���-�� - ������� �� ������������.


[email]:mailto:killer-server@mail.ru
