import pandas as pd
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import train_test_split
from sklearn.metrics import accuracy_score
from sklearn.preprocessing import LabelEncoder
import joblib

# 1. Charger les données
df = pd.read_csv("ml/rentals_export.csv")
print(f"Nombre de lignes chargées : {len(df)}")
print(df.head())

# 2. Préparer les features (X) et la cible (y)
#    On prédit category_id à partir des habitudes de l'utilisateur
X = df[["user_id", "author_id", "jour_semaine_emprunt", "en_retard"]]
y = df["category_id"]

# 3. Séparer en jeu d'entraînement (80%) et jeu de test (20%)
X_train, X_test, y_train, y_test = train_test_split(
    X, y, test_size=0.2, random_state=42
)

# 4. Entraîner le modèle
model = RandomForestClassifier(n_estimators=100, random_state=42)
model.fit(X_train, y_train)

# 5. Évaluer la précision sur les données jamais vues
y_pred = model.predict(X_test)
accuracy = accuracy_score(y_test, y_pred)
print(f"Précision du modèle sur le jeu de test : {accuracy:.2%}")

# 6. Sauvegarder le modèle entraîné pour réutilisation
joblib.dump(model, "ml/model.pkl")
print("Modèle sauvegardé dans ml/model.pkl")

# 7. Générer les prédictions pour chaque utilisateur (catégorie préférée + confiance)
utilisateurs = df["user_id"].unique()
resultats = []

for user_id in utilisateurs:
    donnees_user = df[df["user_id"] == user_id]
    # On prend le profil "moyen"/le plus fréquent de l'utilisateur pour prédire
    author_id_moyen = int(donnees_user["author_id"].mode()[0])
    jour_moyen = int(donnees_user["jour_semaine_emprunt"].mode()[0])
    en_retard_moyen = int(donnees_user["en_retard"].mode()[0])

    profil = pd.DataFrame([{
        "user_id": user_id,
        "author_id": author_id_moyen,
        "jour_semaine_emprunt": jour_moyen,
        "en_retard": en_retard_moyen,
    }])

    proba = model.predict_proba(profil)[0]
    categorie_predite = model.classes_[proba.argmax()]
    confiance = proba.max()

    resultats.append({
        "user_id": user_id,
        "categorie_predite": categorie_predite,
        "confiance": round(confiance * 100, 2),
    })

df_resultats = pd.DataFrame(resultats)
df_resultats.to_csv("ml/predictions.csv", index=False)
print(f"Prédictions générées pour {len(df_resultats)} utilisateurs dans ml/predictions.csv")