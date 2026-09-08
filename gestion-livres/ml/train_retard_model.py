import pandas as pd
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import train_test_split
from sklearn.metrics import accuracy_score

data = pd.read_csv('retard_data.csv')

X = data[['taux_retard_precedent', 'category_id', 'jour_semaine_emprunt', 'mois_emprunt', 'nb_locations_actives_ce_jour']]
y = data['en_retard']

X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

modele = RandomForestClassifier(n_estimators=50, max_depth=4, min_samples_leaf=5, random_state=42)
modele.fit(X_train, y_train)

predictions_test = modele.predict(X_test)
precision = accuracy_score(y_test, predictions_test)
print(f"Precision du modele : {precision * 100:.2f}%")
import joblib
joblib.dump(modele, 'retard_model.pkl')

probabilites = modele.predict_proba(X)[:, 1]

resultats = data.copy()
resultats['risque_retard'] = (probabilites * 100).round(2)

resultats.to_csv('retard_predictions.csv', index=False)

print(f"Termine : predictions calculees pour {len(resultats)} locations")
