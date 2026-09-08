import pandas as pd

data = pd.read_csv('retard_data.csv')

print("Repartition en_retard (0 = a temps, 1 = en retard) :")
print(data['en_retard'].value_counts())
print()
print("Correlation de chaque indice avec en_retard :")
print(data.corr()['en_retard'])